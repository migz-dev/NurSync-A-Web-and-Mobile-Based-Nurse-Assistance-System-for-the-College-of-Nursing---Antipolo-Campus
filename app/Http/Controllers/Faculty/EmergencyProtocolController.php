<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\EmergencyProtocol;
use App\Models\EmergencyProtocolStep;
use App\Models\EmergencyProtocolTag;

class EmergencyProtocolController extends Controller
{
    /**
     * List protocols for the logged-in faculty.
     * Supports search & filters.
     */
    public function index(Request $request)
    {
        $facultyId = auth('faculty')->id();

        $q        = trim((string) $request->get('q', ''));
        $severity = (string) $request->get('severity', '');
        $category = (string) $request->get('category', '');
        $status   = (string) $request->get('status', ''); // draft|published|archived or empty (default: non-archived)
        $ward     = (string) $request->get('ward', '');

        $perPage  = (int) $request->get('per', 12);
        if ($perPage < 6 || $perPage > 60) {
            $perPage = 12;
        }

        $query = EmergencyProtocol::query()
            ->where('faculty_id', $facultyId);

        // Default: hide archived on main index
        if ($status === 'archived') {
            $query->where('status', 'archived');
        } elseif ($status) {
            $query->where('status', $status);
        } else {
            $query->where('status', '!=', 'archived');
        }

        if ($q !== '') {
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder
                    ->where('title', 'like', '%' . $q . '%')
                    ->orWhere('summary', 'like', '%' . $q . '%')
                    ->orWhere('category', 'like', '%' . $q . '%')
                    ->orWhere('ward', 'like', '%' . $q . '%');
            });
        }

        if ($severity !== '') {
            $query->where('severity', $severity);
        }

        if ($category !== '') {
            $query->where('category', $category);
        }

        if ($ward !== '') {
            $query->where('ward', $ward);
        }

        $protocols = $query
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // For filter dropdowns
        $categories = EmergencyProtocol::where('faculty_id', $facultyId)
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        // Unified severities (matches create/edit form)
        $severities = ['Critical', 'Moderate', 'Mild'];

        // Wards list (for filter; also used in create/edit if you want)
        $wards = [
            'Community Health (CHN)',
            'OB Ward',
            'Delivery Room (DR)',
            'Nursery',
            'Pediatrics (PEDIA)',
            'Medical-Surgical (MS)',
            'ICU',
            'Oncology',
            'Isolation Unit',
            'Endocrine Unit',
            'Neurology Unit',
            'Psychiatric (PSYCH)',
            'Emergency Room (ER)',
            'Operating Room (OR)',
            'Trauma Unit',
            'Disaster Response / Community Field',
        ];

        return view('faculty.emergency.index', [
            'protocols'  => $protocols,
            'categories' => $categories,
            'severities' => $severities,
            'wards'      => $wards,
            'filters'    => [
                'q'        => $q,
                'severity' => $severity,
                'category' => $category,
                'status'   => $status,
                'ward'     => $ward,
                'per'      => $perPage,
            ],
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $facultyId = auth('faculty')->id();

        $tags = EmergencyProtocolTag::orderBy('name')->get();
        $severities = ['Critical', 'Moderate', 'Mild'];

        // Optional: you can also pass wards here instead of defining in Blade
        $wards = [
            'Community Health (CHN)',
            'OB Ward',
            'Delivery Room (DR)',
            'Nursery',
            'Pediatrics (PEDIA)',
            'Medical-Surgical (MS)',
            'ICU',
            'Oncology',
            'Isolation Unit',
            'Endocrine Unit',
            'Neurology Unit',
            'Psychiatric (PSYCH)',
            'Emergency Room (ER)',
            'Operating Room (OR)',
            'Trauma Unit',
            'Disaster Response / Community Field',
        ];

        return view('faculty.emergency.create', [
            'tags'       => $tags,
            'severities' => $severities,
            'wards'      => $wards,
        ]);
    }

    /**
     * Store a new protocol (with steps and tags).
     */
public function store(Request $request)
{
    $facultyId = auth('faculty')->id();

    $validated = $request->validate([
        'title'       => ['required', 'string', 'max:255'],
        'category'    => ['nullable', 'string', 'max:100'],
        'ward'        => ['nullable', 'string', 'max:100'],
        'severity'    => ['required', Rule::in(['Critical', 'Moderate', 'Mild'])],
        'summary'     => ['nullable', 'string'],
        'description' => ['nullable', 'string'],
        'video_url'   => ['nullable', 'url', 'max:255'],
        'pdf_path'    => ['nullable', 'string', 'max:255'],
        'status'      => ['required', Rule::in(['draft', 'published'])],
        'steps'                       => ['array'],
        'steps.*.title'               => ['nullable', 'string', 'max:255'],
        'steps.*.description'         => ['nullable', 'string'],
        'steps.*.expected_action'     => ['nullable', 'string'],
        'tag_ids'                     => ['array'],
        'tag_ids.*'                   => ['integer', 'exists:emergency_protocol_tags,id'],
        'new_tags'                    => ['nullable', 'string'],
    ]);

    $baseSlug = Str::slug($validated['title']);
    $slug = $this->generateUniqueSlug($baseSlug);

    DB::transaction(function () use ($validated, $slug, $facultyId) {

        $protocol = EmergencyProtocol::create([
            'faculty_id'  => $facultyId,
            'title'       => $validated['title'],
            'slug'        => $slug,
            'category'    => $validated['category'] ?? null,
            'ward'        => $validated['ward'] ?? null,
            'severity'    => $validated['severity'],
            'summary'     => $validated['summary'] ?? null,
            'description' => $validated['description'] ?? null,
            'video_url'   => $validated['video_url'] ?? null,
            'pdf_path'    => $validated['pdf_path'] ?? null,
            'status'      => $validated['status'],
        ]);

        // ---------------------
        // ✅ Handle Steps
        // ---------------------
        $steps = $validated['steps'] ?? [];
        $stepNo = 1;
        foreach ($steps as $stepData) {
            if (
                empty($stepData['title']) &&
                empty($stepData['description']) &&
                empty($stepData['expected_action'])
            ) {
                continue;
            }

            EmergencyProtocolStep::create([
                'protocol_id'     => $protocol->id,
                'step_no'         => $stepNo++,
                'title'           => $stepData['title'] ?? null,
                'description'     => $stepData['description'] ?? null,
                'expected_action' => $stepData['expected_action'] ?? null,
            ]);
        }

        // ---------------------
        // ✅ Handle Tags (existing + new)
        // ---------------------
        $tagIds = $validated['tag_ids'] ?? [];

        // Handle new tags input (comma-separated)
        if (!empty($validated['new_tags'])) {
            $names = array_filter(array_map('trim', explode(',', $validated['new_tags'])));

            foreach ($names as $name) {
                if ($name === '') continue;
                $tag = EmergencyProtocolTag::firstOrCreate(
                    ['name' => $name],
                    ['color' => 'slate'] // Default color if not provided
                );
                $tagIds[] = $tag->id;
            }
        }

        if (method_exists($protocol, 'tags')) {
            $protocol->tags()->sync($tagIds);
        } else {
            if (!empty($tagIds)) {
                $rows = [];
                foreach ($tagIds as $tagId) {
                    $rows[] = [
                        'protocol_id' => $protocol->id,
                        'tag_id'      => $tagId,
                    ];
                }
                DB::table('emergency_protocol_tag_map')->insert($rows);
            }
        }
    });

    return redirect()
        ->route('faculty.emergency.index')
        ->with('success', 'Emergency protocol created successfully.');
}


    /**
     * Show a specific protocol.
     */
    public function show(string $slug)
    {
        $facultyId = auth('faculty')->id();

        $protocol = EmergencyProtocol::with(['steps' => function ($q) {
                $q->orderBy('step_no');
            }, 'tags'])
            ->where('faculty_id', $facultyId)
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count (non-blocking)
        $protocol->increment('view_count');

        return view('faculty.emergency.show', [
            'protocol' => $protocol,
        ]);
    }

    /**
     * Show edit form.
     */
    public function edit(string $slug)
    {
        $facultyId = auth('faculty')->id();

        $protocol = EmergencyProtocol::with(['steps' => function ($q) {
                $q->orderBy('step_no');
            }, 'tags'])
            ->where('faculty_id', $facultyId)
            ->where('slug', $slug)
            ->firstOrFail();

        $tags = EmergencyProtocolTag::orderBy('name')->get();
        $severities = ['Critical', 'Moderate', 'Mild'];

        $wards = [
            'Community Health (CHN)',
            'OB Ward',
            'Delivery Room (DR)',
            'Nursery',
            'Pediatrics (PEDIA)',
            'Medical-Surgical (MS)',
            'ICU',
            'Oncology',
            'Isolation Unit',
            'Endocrine Unit',
            'Neurology Unit',
            'Psychiatric (PSYCH)',
            'Emergency Room (ER)',
            'Operating Room (OR)',
            'Trauma Unit',
            'Disaster Response / Community Field',
        ];

        return view('faculty.emergency.edit', [
            'protocol'   => $protocol,
            'tags'       => $tags,
            'severities' => $severities,
            'wards'      => $wards,
        ]);
    }

    /**
     * Update an existing protocol.
     */
    public function update(Request $request, string $slug)
    {
        $facultyId = auth('faculty')->id();

        $protocol = EmergencyProtocol::where('faculty_id', $facultyId)
            ->where('slug', $slug)
            ->firstOrFail();

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'category'    => ['nullable', 'string', 'max:100'],
            'ward'        => ['nullable', 'string', 'max:100'],
            'severity'    => ['required', Rule::in(['Critical', 'Moderate', 'Mild'])],
            'summary'     => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'video_url'   => ['nullable', 'url', 'max:255'],
            'pdf_path'    => ['nullable', 'string', 'max:255'],
            'status'      => ['required', Rule::in(['draft', 'published', 'archived'])],

            'steps'                       => ['array'],
            'steps.*.id'                  => ['nullable', 'integer', 'exists:emergency_protocol_steps,id'],
            'steps.*.title'               => ['nullable', 'string', 'max:255'],
            'steps.*.description'         => ['nullable', 'string'],
            'steps.*.expected_action'     => ['nullable', 'string'],

            'tag_ids'                     => ['array'],
            'tag_ids.*'                   => ['integer', 'exists:emergency_protocol_tags,id'],
        ]);

        DB::transaction(function () use ($validated, $protocol) {

            // If title changed, we can regenerate slug (optional).
            if ($protocol->title !== $validated['title']) {
                $baseSlug = Str::slug($validated['title']);
                $protocol->slug = $this->generateUniqueSlug($baseSlug, $protocol->id);
            }

            $protocol->title       = $validated['title'];
            $protocol->category    = $validated['category'] ?? null;
            $protocol->ward        = $validated['ward'] ?? null;
            $protocol->severity    = $validated['severity'];
            $protocol->summary     = $validated['summary'] ?? null;
            $protocol->description = $validated['description'] ?? null;
            $protocol->video_url   = $validated['video_url'] ?? null;
            $protocol->pdf_path    = $validated['pdf_path'] ?? null;
            $protocol->status      = $validated['status'];
            $protocol->save();

            // Steps: simple "replace all" strategy to keep logic easy
            EmergencyProtocolStep::where('protocol_id', $protocol->id)->delete();

            $steps = $validated['steps'] ?? [];
            $stepNo = 1;
            foreach ($steps as $stepData) {
                if (
                    empty($stepData['title']) &&
                    empty($stepData['description']) &&
                    empty($stepData['expected_action'])
                ) {
                    continue;
                }

                EmergencyProtocolStep::create([
                    'protocol_id'     => $protocol->id,
                    'step_no'         => $stepNo++,
                    'title'           => $stepData['title'] ?? null,
                    'description'     => $stepData['description'] ?? null,
                    'expected_action' => $stepData['expected_action'] ?? null,
                ]);
            }

            // Tags
            $tagIds = $validated['tag_ids'] ?? [];
            if (method_exists($protocol, 'tags')) {
                $protocol->tags()->sync($tagIds);
            } else {
                DB::table('emergency_protocol_tag_map')
                    ->where('protocol_id', $protocol->id)
                    ->delete();

                if (!empty($tagIds)) {
                    $rows = [];
                    foreach ($tagIds as $tagId) {
                        $rows[] = [
                            'protocol_id' => $protocol->id,
                            'tag_id'      => $tagId,
                        ];
                    }
                    DB::table('emergency_protocol_tag_map')->insert($rows);
                }
            }
        });

        return redirect()
            ->route('faculty.emergency.show', $protocol->slug)
            ->with('success', 'Emergency protocol updated successfully.');
    }

    /**
     * Archive a protocol (status = archived).
     */
    public function archive(string $slug)
    {
        $facultyId = auth('faculty')->id();

        $protocol = EmergencyProtocol::where('faculty_id', $facultyId)
            ->where('slug', $slug)
            ->firstOrFail();

        $protocol->status = 'archived';
        $protocol->save();

        return redirect()
            ->route('faculty.emergency.index')
            ->with('success', 'Emergency protocol archived.');
    }

    /**
     * List archived protocols.
     */
    public function archivesIndex(Request $request)
    {
        $facultyId = auth('faculty')->id();

        $q       = trim((string) $request->get('q', ''));
        $perPage = (int) $request->get('per', 12);
        if ($perPage < 6 || $perPage > 60) {
            $perPage = 12;
        }

        $query = EmergencyProtocol::where('faculty_id', $facultyId)
            ->where('status', 'archived');

        if ($q !== '') {
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder
                    ->where('title', 'like', '%' . $q . '%')
                    ->orWhere('summary', 'like', '%' . $q . '%')
                    ->orWhere('category', 'like', '%' . $q . '%')
                    ->orWhere('ward', 'like', '%' . $q . '%');
            });
        }

        $protocols = $query
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('faculty.emergency.archives', [
            'protocols' => $protocols,
            'filters'   => [
                'q'   => $q,
                'per' => $perPage,
            ],
        ]);
    }

    /**
     * Helper to generate a unique slug.
     */
    protected function generateUniqueSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $slug = $baseSlug !== '' ? $baseSlug : 'protocol';

        $i = 1;
        while (true) {
            $query = EmergencyProtocol::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                return $slug;
            }

            $slug = $baseSlug . '-' . $i;
            $i++;
        }
    }
}
