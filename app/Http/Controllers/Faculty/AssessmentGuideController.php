<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\AssessmentGuide;

class AssessmentGuideController extends Controller
{
    public function __construct()
    {
        // CI / Faculty guard
        $this->middleware('auth:faculty');
    }

    /**
     * List assessment guides for the logged-in faculty.
     * Supports search + status filter.
     */
    public function index(Request $request)
    {
        $facultyId = auth('faculty')->id();

        $search = trim((string) $request->input('q', ''));
        $status = $request->input('status', 'all'); // all | draft | published | archived

        $query = AssessmentGuide::query()
            ->where('faculty_id', $facultyId)
            ->orderByDesc('updated_at')
            ->orderByDesc('id');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $like = '%' . $search . '%';

            $query->where(function ($q) use ($like, $search) {
                $q->where('title', 'like', $like)
                  ->orWhere('summary', 'like', $like)
                  ->orWhere('content_rubric', 'like', $like)
                  ->orWhere('content_documentation', 'like', $like)
                  ->orWhere('content_tips', 'like', $like)
                  ->orWhere('content_mistakes', 'like', $like);

                // If your MySQL supports FULLTEXT and you want to use it instead:
                // $q->orWhereRaw("MATCH(title, summary, content_rubric, content_documentation, content_tips, content_mistakes) AGAINST (? IN BOOLEAN MODE)", [$search . '*']);
            });
        }

        // For now just get all (client-side pagination in Blade, like Ward Orientation)
        $guides = $query->get();

        return view('faculty.assessment_guides.index', [
            'guides' => $guides,
            'filters' => [
                'q'      => $search,
                'status' => $status,
            ],
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('faculty.assessment_guides.create');
    }

    /**
     * Store a new assessment guide.
     */
    public function store(Request $request)
    {
        $facultyId = auth('faculty')->id();

        $data = $request->validate([
            'title'               => ['required', 'string', 'max:255'],
            'summary'             => ['nullable', 'string'],
            'content_rubric'      => ['nullable', 'string'],
            'content_documentation' => ['nullable', 'string'],
            'content_tips'        => ['nullable', 'string'],
            'content_mistakes'    => ['nullable', 'string'],
            'status'              => ['required', Rule::in(['draft', 'published', 'archived'])],
            'tags'                => ['nullable', 'string'], // comma-separated or space-separated input from form
        ]);

        // Parse tags into array
        $tagsArray = $this->parseTagsToArray($data['tags'] ?? null);

        DB::transaction(function () use ($facultyId, $data, $tagsArray) {
            $guide = new AssessmentGuide();
            $guide->faculty_id = $facultyId;
            $guide->title = $data['title'];
            $guide->summary = $data['summary'] ?? null;
            $guide->content_rubric = $data['content_rubric'] ?? null;
            $guide->content_documentation = $data['content_documentation'] ?? null;
            $guide->content_tips = $data['content_tips'] ?? null;
            $guide->content_mistakes = $data['content_mistakes'] ?? null;
            $guide->status = $data['status'] ?? 'draft';
            $guide->tags_json = !empty($tagsArray) ? json_encode($tagsArray) : null;
            $guide->save();
        });

return redirect()
    ->route('faculty.instructor.assessment.index')
    ->with('success', 'Assessment guide created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(AssessmentGuide $assessmentGuide)
    {
        $this->ensureOwnership($assessmentGuide);

        // Decode tags for form (e.g., join with comma)
        $tags = [];
        if (!empty($assessmentGuide->tags_json)) {
            $decoded = json_decode($assessmentGuide->tags_json, true);
            if (is_array($decoded)) {
                $tags = $decoded;
            }
        }

        return view('faculty.assessment_guides.edit', [
            'guide' => $assessmentGuide,
            'tags'  => $tags,
        ]);
    }

    /**
     * Update an existing assessment guide.
     */
    public function update(Request $request, AssessmentGuide $assessmentGuide)
    {
        $this->ensureOwnership($assessmentGuide);

        $data = $request->validate([
            'title'               => ['required', 'string', 'max:255'],
            'summary'             => ['nullable', 'string'],
            'content_rubric'      => ['nullable', 'string'],
            'content_documentation' => ['nullable', 'string'],
            'content_tips'        => ['nullable', 'string'],
            'content_mistakes'    => ['nullable', 'string'],
            'status'              => ['required', Rule::in(['draft', 'published', 'archived'])],
            'tags'                => ['nullable', 'string'],
        ]);

        $tagsArray = $this->parseTagsToArray($data['tags'] ?? null);

        DB::transaction(function () use ($assessmentGuide, $data, $tagsArray) {
            $assessmentGuide->title = $data['title'];
            $assessmentGuide->summary = $data['summary'] ?? null;
            $assessmentGuide->content_rubric = $data['content_rubric'] ?? null;
            $assessmentGuide->content_documentation = $data['content_documentation'] ?? null;
            $assessmentGuide->content_tips = $data['content_tips'] ?? null;
            $assessmentGuide->content_mistakes = $data['content_mistakes'] ?? null;
            $assessmentGuide->status = $data['status'] ?? 'draft';
            $assessmentGuide->tags_json = !empty($tagsArray) ? json_encode($tagsArray) : null;
            $assessmentGuide->save();
        });

return redirect()
    ->route('faculty.instructor.assessment.index')
    ->with('success', 'Assessment guide updated successfully.');
    }

    /**
     * Archive (soft delete) an assessment guide.
     * For CI mode we'll just mark status = archived.
     */
    public function destroy(AssessmentGuide $assessmentGuide)
    {
        $this->ensureOwnership($assessmentGuide);

        $assessmentGuide->status = 'archived';
        $assessmentGuide->save();

return redirect()
    ->route('faculty.instructor.assessment.index')
    ->with('success', 'Assessment guide archived.');
    }

    /**
     * Quick publish/unpublish endpoint (optional).
     */
    public function updateStatus(Request $request, AssessmentGuide $assessmentGuide)
    {
        $this->ensureOwnership($assessmentGuide);

        $data = $request->validate([
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ]);

        $assessmentGuide->status = $data['status'];
        $assessmentGuide->save();

        return back()->with('success', 'Status updated.');
    }

    /**
     * Ensure the logged-in faculty owns the guide.
     */
    protected function ensureOwnership(AssessmentGuide $guide): void
    {
        $facultyId = auth('faculty')->id();
        if (!$facultyId || $guide->faculty_id !== $facultyId) {
            abort(403, 'You are not allowed to access this assessment guide.');
        }
    }

    /**
     * Parse tags from a free-text field into an array.
     * Accepts comma-separated or space-separated input.
     */
    protected function parseTagsToArray(?string $raw): array
    {
        if ($raw === null || trim($raw) === '') {
            return [];
        }

        // Replace commas with spaces, split on whitespace
        $normalized = str_replace([',', ';'], ' ', $raw);
        $parts = preg_split('/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $tags = [];
        foreach ($parts as $part) {
            $clean = trim($part);
            if ($clean !== '') {
                $tags[] = $clean;
            }
        }

        // Unique, preserve order
        return array_values(array_unique($tags));
    }
}