<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NursingReference;
use Illuminate\Http\Request;

class NursingReferenceAdminController extends Controller
{
    /**
     * Display a listing of the nursing references.
     */
    public function index(Request $request)
    {
        $q        = trim((string) $request->input('q', ''));
        $category = $request->input('category');
        $source   = $request->input('source');
        $perPage  = (int) $request->input('per_page', 10);
        if ($perPage <= 0 || $perPage > 100) {
            $perPage = 10;
        }

        $query = NursingReference::query();

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('url', 'like', "%{$q}%")
                    ->orWhere('source', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if (!empty($category)) {
            $query->where('category', $category);
        }

        if (!empty($source)) {
            $query->where('source', $source);
        }

        $query->orderBy('is_featured', 'desc')
              ->orderBy('title');

        $items = $query->paginate($perPage)->appends($request->query());

        // For filters
        $categories = NursingReference::query()
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $sources = NursingReference::query()
            ->select('source')
            ->whereNotNull('source')
            ->where('source', '!=', '')
            ->distinct()
            ->orderBy('source')
            ->pluck('source');

        // AJAX: return JSON with rendered partials
        if ($request->ajax()) {
            $rows  = view('admin.nursing_references._rows', compact('items'))->render();
            $pager = view('admin.nursing_references._pager', compact('items'))->render();

            return response()->json([
                'rows'  => $rows,
                'pager' => $pager,
            ]);
        }

        // Full page
        return view('admin.nursing_references.index', compact('items', 'categories', 'sources'));
    }

    /**
     * Show the form for creating a new nursing reference.
     */
    public function create()
    {
        $ref = new NursingReference();

        // For datalists in the form
        $categories = NursingReference::query()
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $sources = NursingReference::query()
            ->select('source')
            ->whereNotNull('source')
            ->where('source', '!=', '')
            ->distinct()
            ->orderBy('source')
            ->pluck('source');

        $tagsText = ''; // none yet

        return view('admin.nursing_references.form', [
            'ref'        => $ref,
            'mode'       => 'create',
            'pageTitle'  => 'Create Nursing Reference',
            'categories' => $categories,
            'sources'    => $sources,
            'tagsText'   => $tagsText,
        ]);
    }

    /**
     * Store a newly created nursing reference in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request);

        // Normalize checkboxes
        $data['is_featured'] = $request->boolean('is_featured');
        // Default active = true unless explicitly unchecked (you can tweak if needed)
        $data['is_active']   = $request->has('is_active')
            ? $request->boolean('is_active')
            : true;

        // Tags (comma-separated text → JSON array)
        $tagsText = (string) $request->input('tags_text', '');
        $tags = collect(explode(',', $tagsText))
            ->map(fn ($t) => trim($t))
            ->filter()
            ->unique()
            ->values()
            ->all();
        $data['tags_json'] = $tags ? json_encode($tags) : null;

        NursingReference::create($data);

        return redirect()
            ->route('admin.nursing_references.index')
            ->with('ok', 'Nursing reference created successfully.');
    }

    /**
     * Display a single reference (optional – can just redirect to edit).
     */
    public function show(NursingReference $reference)
    {
        // You can either show a read-only page or redirect to edit:
        return redirect()->route('admin.nursing_references.edit', $reference->id);
    }

    /**
     * Show the form for editing the specified reference.
     */
    public function edit(NursingReference $reference)
    {
        $ref = $reference;

        // For datalists in the form
        $categories = NursingReference::query()
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $sources = NursingReference::query()
            ->select('source')
            ->whereNotNull('source')
            ->where('source', '!=', '')
            ->distinct()
            ->orderBy('source')
            ->pluck('source');

        // Pre-fill tags_text from tags_json
        $tags = [];
        if (!empty($ref->tags_json)) {
            $decoded = is_array($ref->tags_json)
                ? $ref->tags_json
                : json_decode($ref->tags_json, true);

            if (is_array($decoded)) {
                $tags = $decoded;
            }
        }
        $tagsText = implode(', ', $tags);

        return view('admin.nursing_references.form', [
            'ref'        => $ref,
            'mode'       => 'edit',
            'pageTitle'  => 'Edit Nursing Reference',
            'categories' => $categories,
            'sources'    => $sources,
            'tagsText'   => $tagsText,
        ]);
    }

    /**
     * Update the specified reference in storage.
     */
    public function update(Request $request, NursingReference $reference)
    {
        $data = $this->validateData($request);

        // Normalize checkboxes
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->has('is_active')
            ? $request->boolean('is_active')
            : true;

        // Tags (comma-separated text → JSON array)
        $tagsText = (string) $request->input('tags_text', '');
        $tags = collect(explode(',', $tagsText))
            ->map(fn ($t) => trim($t))
            ->filter()
            ->unique()
            ->values()
            ->all();
        $data['tags_json'] = $tags ? json_encode($tags) : null;

        $reference->update($data);

        return redirect()
            ->route('admin.nursing_references.index')
            ->with('ok', 'Nursing reference updated successfully.');
    }

    /**
     * Remove the specified reference from storage.
     */
    public function destroy(NursingReference $reference)
    {
        $title = $reference->title;
        $reference->delete();

        return redirect()
            ->route('admin.nursing_references.index')
            ->with('ok', "Nursing reference \"{$title}\" deleted.");
    }

    /**
     * Shared validation rules for create/update.
     */
    protected function validateData(Request $request): array
    {
        return $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'category'    => ['required', 'string', 'max:100'],
            'url'         => ['required', 'string', 'max:500', 'url'],
            'source'      => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            // booleans handled manually so unchecked checkboxes become 0/true defaults as needed
            'is_featured' => ['sometimes', 'boolean'],
            'is_active'   => ['sometimes', 'boolean'],
        ], [], [
            'title'    => 'Title',
            'category' => 'Category',
            'url'      => 'URL',
        ]);
    }
}
