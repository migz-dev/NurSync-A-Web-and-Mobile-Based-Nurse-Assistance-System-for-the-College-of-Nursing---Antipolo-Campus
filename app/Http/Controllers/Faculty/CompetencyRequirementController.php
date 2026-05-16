<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\CompetencyCategory;
use App\Models\CompetencyItem;
use App\Models\CompetencyRotationSkill;
use App\Models\CompetencyCaseRequirement;
use App\Models\CompetencyExplanation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CompetencyRequirementController extends Controller
{
    /**
     * List competency items (CI view).
     * Supports search + filters (status, category, rotation).
     */
    public function index(Request $request)
    {
        $q          = trim((string) $request->query('q', ''));
        $status     = $request->query('status', 'all');      // all | draft | published
        $categoryId = $request->query('category_id', 'all'); // all or specific ID
        $rotation   = $request->query('rotation', 'all');    // all or "DR" / "OR" / etc.

        // Base query: exclude archived from the main CI list (like Assessment Guides)
        $query = CompetencyItem::query()
            ->with('category')
            ->where('status', '!=', 'archived');

        // Search by title/description/reason
        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($inner) use ($like) {
                $inner->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('reason', 'like', $like);
            });
        }

        // Filter by status
        if (in_array($status, ['draft', 'published'], true)) {
            $query->where('status', $status);
        }

        // Filter by category
        if ($categoryId !== 'all' && $categoryId !== null && $categoryId !== '') {
            $query->where('category_id', $categoryId);
        }

        // Rotation filter placeholder (ready for future enhancements)
        if ($rotation !== 'all' && $rotation !== null && $rotation !== '') {
            $query->whereHas('explanations', function ($inner) use ($rotation) {
                // When you decide how rotations bind to competencies,
                // you can move the filter logic here.
            });
        }

        $items = $query
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        // Filters data for the UI
        $categories = CompetencyCategory::orderBy('title')->get();
        $rotations  = CompetencyRotationSkill::query()
            ->select('rotation')
            ->distinct()
            ->orderBy('rotation')
            ->pluck('rotation')
            ->values();

        $filters = [
            'q'           => $q,
            'status'      => $status,
            'category_id' => $categoryId,
            'rotation'    => $rotation,
        ];

        return view('faculty.competencies.index', [
            'items'      => $items,
            'categories' => $categories,
            'rotations'  => $rotations,
            'filters'    => $filters,
        ]);
    }

    /**
     * Show the create form for a new competency item.
     */
    public function create()
    {
        $categories = CompetencyCategory::orderBy('title')->get();

        // Rotations list for select chips (you can adjust as needed)
        $rotations = [
            'CHN', 'OB', 'DR', 'PEDIA', 'CDN', 'ONCO',
            'MS', 'OR', 'GERIA', 'ORTHO', 'PSYCH',
            'ICU', 'ER', 'DN', 'MEDICINE', 'SURGERY',
        ];

        return view('faculty.competencies.create', [
            'categories' => $categories,
            'rotations'  => $rotations,
        ]);
    }

    /**
     * Store a new competency item + optional explanations.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:competency_categories,id'],
            'description' => ['nullable', 'string'],
            'reason'      => ['nullable', 'string'],
            'status'      => ['required', Rule::in(['draft', 'published'])],

            // Explanations arrays
            'explanations'            => ['nullable', 'array'],
            'explanations.*.title'   => ['nullable', 'string', 'max:255'],
            'explanations.*.content' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();

        try {
            // Create the main competency item
            $item = CompetencyItem::create([
                'category_id' => $validated['category_id'],
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? null,
                'reason'      => $validated['reason'] ?? null,
                'status'      => $validated['status'],
            ]);

            // Optional nurse explanations
            if (!empty($validated['explanations']) && is_array($validated['explanations'])) {
                foreach ($validated['explanations'] as $exp) {
                    $title   = $exp['title']   ?? null;
                    $content = $exp['content'] ?? null;

                    // Skip empty rows
                    if (!$title && !$content) {
                        continue;
                    }

                    CompetencyExplanation::create([
                        'competency_item_id' => $item->id,
                        'title'              => $title ?: 'Additional insight',
                        'content'            => $content ?: '',
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('faculty.instructor.competencies.index')
                ->with('success', 'Competency requirement created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()
                ->withErrors('Something went wrong while saving the competency requirement.')
                ->withInput();
        }
    }

    /**
     * Edit a competency item (CI view).
     */
    public function edit(CompetencyItem $competency)
    {
        $competency->load(['category', 'explanations']);

        $categories = CompetencyCategory::orderBy('title')->get();

        $rotations = [
            'CHN', 'OB', 'DR', 'PEDIA', 'CDN', 'ONCO',
            'MS', 'OR', 'GERIA', 'ORTHO', 'PSYCH',
            'ICU', 'ER', 'DN', 'MEDICINE', 'SURGERY',
        ];

        return view('faculty.competencies.edit', [
            'item'       => $competency,
            'categories' => $categories,
            'rotations'  => $rotations,
        ]);
    }

    /**
     * Update an existing competency item + explanations.
     */
    public function update(Request $request, CompetencyItem $competency)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:competency_categories,id'],
            'description' => ['nullable', 'string'],
            'reason'      => ['nullable', 'string'],
            'status'      => ['required', Rule::in(['draft', 'published'])],

            'explanations'            => ['nullable', 'array'],
            'explanations.*.id'       => ['nullable', 'integer', 'exists:competency_explanations,id'],
            'explanations.*.title'    => ['nullable', 'string', 'max:255'],
            'explanations.*.content'  => ['nullable', 'string'],
        ]);

        DB::beginTransaction();

        try {
            $competency->update([
                'category_id' => $validated['category_id'],
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? null,
                'reason'      => $validated['reason'] ?? null,
                'status'      => $validated['status'],
            ]);

            // Sync explanations
            $existingIds = [];

            if (!empty($validated['explanations']) && is_array($validated['explanations'])) {
                foreach ($validated['explanations'] as $exp) {
                    $expId   = $exp['id'] ?? null;
                    $title   = $exp['title'] ?? null;
                    $content = $exp['content'] ?? null;

                    // Empty fields with an ID mean "delete this explanation"
                    if (!$title && !$content && $expId) {
                        CompetencyExplanation::where('id', $expId)
                            ->where('competency_item_id', $competency->id)
                            ->delete();
                        continue;
                    }

                    if ($expId) {
                        // Update existing explanation
                        $model = CompetencyExplanation::where('id', $expId)
                            ->where('competency_item_id', $competency->id)
                            ->first();

                        if ($model) {
                            $model->update([
                                'title'   => $title ?: 'Additional insight',
                                'content' => $content ?: '',
                            ]);
                            $existingIds[] = $model->id;
                        }
                    } else {
                        // Create new explanation
                        $model = CompetencyExplanation::create([
                            'competency_item_id' => $competency->id,
                            'title'              => $title ?: 'Additional insight',
                            'content'            => $content ?: '',
                        ]);
                        $existingIds[] = $model->id;
                    }
                }
            }

            // Optional strict sync: delete explanations not present in the form
            // CompetencyExplanation::where('competency_item_id', $competency->id)
            //     ->whereNotIn('id', $existingIds)
            //     ->delete();

            DB::commit();

            return redirect()
                ->route('faculty.instructor.competencies.index')
                ->with('success', 'Competency requirement updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()
                ->withErrors('Something went wrong while updating the competency requirement.')
                ->withInput();
        }
    }

    /**
     * Archive a competency item (soft archive using status).
     * This mirrors how Assessment Guides hide archived records.
     */
    public function destroy(CompetencyItem $competency)
    {
        try {
            $competency->update([
                'status' => 'archived',
            ]);

            return redirect()
                ->route('faculty.instructor.competencies.index')
                ->with('success', 'Competency requirement archived successfully.');
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withErrors('Failed to archive this competency requirement.');
        }
    }

    /**
     * Store a new category (AJAX from the create competency form).
     *
     * Route name used in Blade:
     * faculty.instructor.competencies.categories.store
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                'unique:competency_categories,title',
            ],
        ]);

        try {
            $category = CompetencyCategory::create([
                'title'       => $validated['title'],
                'description' => null,
            ]);

            // For fetch() in the Blade we always want JSON.
            return response()->json([
                'id'    => $category->id,
                'title' => $category->title,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Failed to create category.',
            ], 500);
        }
    }
}
