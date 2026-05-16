<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmergencyProtocol;
use App\Models\EmergencyProtocolStep;
use App\Models\EmergencyProtocolTag;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EmergencyProtocolAdminController extends Controller
{
    /**
     * List all emergency protocols (admin-wide).
     * - Default: hide archived
     * - Supports AJAX (JSON) for table reloads.
     */
    public function index(Request $request)
    {
        $q         = trim((string) $request->get('q', ''));
        $severity  = (string) $request->get('severity', '');
        $category  = (string) $request->get('category', '');
        $status    = (string) $request->get('status', '');
        $ward      = (string) $request->get('ward', '');
        $facultyId = (string) $request->get('faculty_id', '');

        $perPage   = (int) $request->get('per', 15);
        if ($perPage < 5 || $perPage > 100) {
            $perPage = 15;
        }

        $query = EmergencyProtocol::query()
            ->with(['faculty', 'createdByAdmin'])
            ->orderBy('updated_at', 'desc');

        // Status filter (default: hide archived)
        if ($status === 'archived') {
            $query->where('status', 'archived');
        } elseif ($status) {
            $query->where('status', $status);
        } else {
            $query->where('status', '!=', 'archived');
        }

        if ($q !== '') {
            $query->where(function ($qb) use ($q) {
                $qb->where('title', 'like', "%{$q}%")
                    ->orWhere('summary', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%")
                    ->orWhere('ward', 'like', "%{$q}%");
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
        if ($facultyId !== '') {
            $query->where('faculty_id', $facultyId);
        }

        $protocols = $query->paginate($perPage)->withQueryString();

        // Dropdown data
        $categories = EmergencyProtocol::select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $wards = EmergencyProtocol::select('ward')
            ->whereNotNull('ward')
            ->where('ward', '!=', '')
            ->distinct()
            ->orderBy('ward')
            ->pluck('ward');

        $severities = ['Critical', 'Moderate', 'Mild'];
        $faculties  = Faculty::orderBy('full_name')->get(['id', 'full_name']);

        $filters = [
            'q'          => $q,
            'severity'   => $severity,
            'category'   => $category,
            'status'     => $status,
            'ward'       => $ward,
            'faculty_id' => $facultyId,
            'per'        => $perPage,
        ];

        // 🔁 AJAX: used by epFetchList() in the Blade JS
        if ($request->wantsJson()) {
            return response()->json([
                'rows'  => view('admin.emergency._rows', [
                    'protocols' => $protocols,
                ])->render(),
                'pager' => view('admin.emergency._pager', [
                    'protocols' => $protocols,
                ])->render(),
            ]);
        }

        // Full page
        return view('admin.emergency.index', [
            'protocols'  => $protocols,
            'categories' => $categories,
            'wards'      => $wards,
            'severities' => $severities,
            'faculties'  => $faculties,
            'filters'    => $filters,
        ]);
    }

    /**
     * Archived emergency protocols list (admin).
     * - Always locked to status = 'archived'
     * - Mirrors index() UI + AJAX behavior.
     */
    public function archived(Request $request)
    {
        $q         = trim((string) $request->get('q', ''));
        $severity  = (string) $request->get('severity', '');
        $category  = (string) $request->get('category', '');
        $ward      = (string) $request->get('ward', '');
        $facultyId = (string) $request->get('faculty_id', '');

        $perPage   = (int) $request->get('per', 15);
        if ($perPage < 5 || $perPage > 100) {
            $perPage = 15;
        }

        $query = EmergencyProtocol::query()
            ->with(['faculty', 'createdByAdmin'])
            ->where('status', 'archived') // 🔒 lock to archived
            ->orderBy('updated_at', 'desc');

        if ($q !== '') {
            $query->where(function ($qb) use ($q) {
                $qb->where('title', 'like', "%{$q}%")
                    ->orWhere('summary', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%")
                    ->orWhere('ward', 'like', "%{$q}%");
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
        if ($facultyId !== '') {
            $query->where('faculty_id', $facultyId);
        }

        $protocols = $query->paginate($perPage)->withQueryString();

        // Dropdown data (same as index)
        $categories = EmergencyProtocol::select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $wards = EmergencyProtocol::select('ward')
            ->whereNotNull('ward')
            ->where('ward', '!=', '')
            ->distinct()
            ->orderBy('ward')
            ->pluck('ward');

        $severities = ['Critical', 'Moderate', 'Mild'];
        $faculties  = Faculty::orderBy('full_name')->get(['id', 'full_name']);

        $filters = [
            'q'          => $q,
            'severity'   => $severity,
            'category'   => $category,
            'status'     => 'archived',
            'ward'       => $ward,
            'faculty_id' => $facultyId,
            'per'        => $perPage,
        ];

        // 🔁 AJAX: used by epFetchList() on archived.blade.php
        if ($request->wantsJson()) {
            return response()->json([
                'rows'  => view('admin.emergency._rows', [
                    'protocols' => $protocols,
                ])->render(),
                'pager' => view('admin.emergency._pager', [
                    'protocols' => $protocols,
                ])->render(),
            ]);
        }

        // Full page (mirrors index UI, but in archived view)
        return view('admin.emergency.archived', [
            'protocols'  => $protocols,
            'categories' => $categories,
            'wards'      => $wards,
            'severities' => $severities,
            'faculties'  => $faculties,
            'filters'    => $filters,
        ]);
    }

    /**
     * Show create form (admin can choose faculty owner).
     */
    public function create()
    {
        $severities = ['Critical', 'Moderate', 'Mild'];
        $tags       = EmergencyProtocolTag::orderBy('name')->get();
        $faculties  = Faculty::orderBy('full_name')->get(['id', 'full_name']);

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

        return view('admin.emergency.create', compact('severities', 'tags', 'faculties', 'wards'));
    }

    /**
     * Store a new protocol (admin may leave faculty_id blank = admin-owned).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // NOTE: table is `faculty`, not `faculties`
            'faculty_id'  => ['nullable', 'integer', 'exists:faculty,id'],
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

            'tag_ids'   => ['array'],
            'tag_ids.*' => ['integer', 'exists:emergency_protocol_tags,id'],
            'new_tags'  => ['nullable', 'string'],
        ]);

        $slug = $this->generateUniqueSlug(Str::slug($validated['title']));

        DB::transaction(function () use ($validated, $slug) {
            $data = [
                // null = admin-owned (faculty_id is nullable in DB)
                'faculty_id'  => $validated['faculty_id'] ?? null,
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
            ];

            // Record admin creator if column exists.
            if (Schema::hasColumn('emergency_protocols', 'created_by_admin_id') && auth('admin')->check()) {
                $data['created_by_admin_id'] = auth('admin')->id();
            }

            $protocol = EmergencyProtocol::create($data);

            // Steps (insert in given order)
            $steps  = $validated['steps'] ?? [];
            $no     = 1;
            foreach ($steps as $s) {
                if (empty($s['title']) && empty($s['description']) && empty($s['expected_action'])) {
                    continue;
                }

                EmergencyProtocolStep::create([
                    'protocol_id'     => $protocol->id,
                    'step_no'         => $no++,
                    'title'           => $s['title'] ?? null,
                    'description'     => $s['description'] ?? null,
                    'expected_action' => $s['expected_action'] ?? null,
                ]);
            }

            // Tags (existing + new)
            $tagIds = $validated['tag_ids'] ?? [];
            if (!empty($validated['new_tags'])) {
                foreach (array_filter(array_map('trim', explode(',', $validated['new_tags']))) as $name) {
                    if ($name === '') {
                        continue;
                    }
                    $tagIds[] = EmergencyProtocolTag::firstOrCreate(
                        ['name' => $name],
                        ['color' => 'slate']
                    )->id;
                }
            }

            if (method_exists($protocol, 'tags')) {
                $protocol->tags()->sync($tagIds);
            } elseif (!empty($tagIds)) {
                $rows = [];
                foreach ($tagIds as $tagId) {
                    $rows[] = ['protocol_id' => $protocol->id, 'tag_id' => $tagId];
                }
                DB::table('emergency_protocol_tag_map')->insert($rows);
            }
        });

        return redirect()
            ->route('admin.emergency_protocols.index')
            ->with('success', 'Emergency protocol created successfully.');
    }

    /**
     * Show protocol (admin).
     */
    public function show(EmergencyProtocol $protocol)
    {
        $protocol->load([
            'steps' => fn ($q) => $q->orderBy('step_no'),
            'tags',
            'faculty',
            'createdByAdmin',
        ]);

        return view('admin.emergency.show', compact('protocol'));
    }

    /**
     * Edit form (admin).
     */
    public function edit(EmergencyProtocol $protocol)
    {
        $protocol->load([
            'steps' => fn ($q) => $q->orderBy('step_no'),
            'tags',
            'faculty',
            'createdByAdmin',
        ]);

        $severities = ['Critical', 'Moderate', 'Mild'];
        $tags       = EmergencyProtocolTag::orderBy('name')->get();
        $faculties  = Faculty::orderBy('full_name')->get(['id', 'full_name']);

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

        return view('admin.emergency.edit', compact('protocol', 'severities', 'tags', 'faculties', 'wards'));
    }

    /**
     * Update protocol (admin). faculty_id remains optional.
     */
    public function update(Request $request, EmergencyProtocol $protocol)
    {
        $validated = $request->validate([
            // NOTE: table is `faculty`, not `faculties`
            'faculty_id'  => ['nullable', 'integer', 'exists:faculty,id'],
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
            'steps.*.title'               => ['nullable', 'string', 'max:255'],
            'steps.*.description'         => ['nullable', 'string'],
            'steps.*.expected_action'     => ['nullable', 'string'],

            'tag_ids'   => ['array'],
            'tag_ids.*' => ['integer', 'exists:emergency_protocol_tags,id'],
            'new_tags'  => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $protocol) {
            // Core fields
            $protocol->faculty_id  = $validated['faculty_id'] ?? null;
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

            // Steps: replace all with new order for simplicity
            EmergencyProtocolStep::where('protocol_id', $protocol->id)->delete();
            $no = 1;
            foreach (($validated['steps'] ?? []) as $s) {
                if (empty($s['title']) && empty($s['description']) && empty($s['expected_action'])) {
                    continue;
                }

                EmergencyProtocolStep::create([
                    'protocol_id'     => $protocol->id,
                    'step_no'         => $no++,
                    'title'           => $s['title'] ?? null,
                    'description'     => $s['description'] ?? null,
                    'expected_action' => $s['expected_action'] ?? null,
                ]);
            }

            // Tags: sync existing + create new
            $tagIds = $validated['tag_ids'] ?? [];
            if (!empty($validated['new_tags'])) {
                foreach (array_filter(array_map('trim', explode(',', $validated['new_tags']))) as $name) {
                    if ($name === '') {
                        continue;
                    }
                    $tagIds[] = EmergencyProtocolTag::firstOrCreate(
                        ['name' => $name],
                        ['color' => 'slate']
                    )->id;
                }
            }

            if (method_exists($protocol, 'tags')) {
                $protocol->tags()->sync($tagIds);
            } else {
                // fallback: clear then insert pivot rows
                DB::table('emergency_protocol_tag_map')
                    ->where('protocol_id', $protocol->id)
                    ->delete();

                if (!empty($tagIds)) {
                    $rows = [];
                    foreach ($tagIds as $tagId) {
                        $rows[] = ['protocol_id' => $protocol->id, 'tag_id' => $tagId];
                    }
                    DB::table('emergency_protocol_tag_map')->insert($rows);
                }
            }
        });

        return redirect()
            ->route('admin.emergency_protocols.show', $protocol->id)
            ->with('ok', 'Emergency protocol updated successfully.');
    }

    public function archive(EmergencyProtocol $protocol)
    {
        $protocol->status = 'archived';
        $protocol->save();

        return redirect()
            ->route('admin.emergency_protocols.index')
            ->with('success', 'Protocol archived.');
    }

    public function restore(EmergencyProtocol $protocol)
    {
        if ($protocol->status === 'archived') {
            $protocol->status = 'draft';
            $protocol->save();
        }

        return redirect()
            ->route('admin.emergency_protocols.show', $protocol->id)
            ->with('success', 'Protocol restored.');
    }

    public function destroy(EmergencyProtocol $protocol)
    {
        $protocol->delete();

        return redirect()
            ->route('admin.emergency_protocols.index')
            ->with('success', 'Protocol deleted permanently.');
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
