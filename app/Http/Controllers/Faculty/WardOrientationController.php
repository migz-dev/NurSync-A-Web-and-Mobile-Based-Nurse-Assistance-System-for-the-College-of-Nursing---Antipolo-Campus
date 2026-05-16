<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\WardOrientation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WardOrientationController extends Controller
{
    /**
     * List all non-archived orientations owned by the CI.
     */
    public function index(Request $request)
    {
        $faculty = Auth::guard('faculty')->user();

        $orientations = WardOrientation::query()
            ->notArchived()
            ->ownedBy($faculty->id)
            ->orderBy('ward_code')
            ->orderBy('title')
            ->get();

        return view('faculty.ward_orientation.index', [
            'orientations' => $orientations,
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $wardOptions = $this->wardOptions();

        return view('faculty.ward_orientation.create', [
            'wardOptions' => $wardOptions,
        ]);
    }

    /**
     * Store new orientation.
     */
    public function store(Request $request)
    {
        $faculty = Auth::guard('faculty')->user();

        $data = $this->validateRequest($request);

        // status based on action
        $action = $request->input('action', 'draft'); // 'draft' | 'publish'
        $status = $action === 'publish'
            ? WardOrientation::STATUS_PUBLISHED
            : WardOrientation::STATUS_DRAFT;

        $slugBase = Str::slug($data['title'] . '-' . $data['ward_code']);
        $slug = $this->makeUniqueSlug($slugBase);

        $orientation = WardOrientation::create([
            'ward_code'              => $data['ward_code'],
            'title'                  => $data['title'],
            'slug'                   => $slug,
            'summary'                => $data['summary'] ?? null,
            'culture_text'           => $data['culture_text'] ?? null,
            'routines_text'          => $data['routines_text'] ?? null,
            'patient_cases_text'     => $data['patient_cases_text'] ?? null,
            'workload_text'          => $data['workload_text'] ?? null,
            'emergencies_text'       => $data['emergencies_text'] ?? null,
            'layout_locations_text'  => $data['layout_locations_text'] ?? null,
            'tips_text'              => $data['tips_text'] ?? null,
            'estimated_watch_minutes'=> $data['estimated_watch_minutes'] ?? null,
            'status'                 => $status,
            'created_by_faculty_id'  => $faculty->id,
            'published_at'           => $status === WardOrientation::STATUS_PUBLISHED ? now() : null,
        ]);

        return redirect()
            ->route('faculty.instructor.orientation.index')
            ->with('success', $status === WardOrientation::STATUS_PUBLISHED
                ? 'Ward orientation published successfully.'
                : 'Ward orientation saved as draft.');
    }

    /**
     * Show edit form.
     */
    public function edit(WardOrientation $orientation)
    {
        $faculty = Auth::guard('faculty')->user();

        if ($orientation->created_by_faculty_id !== $faculty->id) {
            abort(403);
        }

        $wardOptions = $this->wardOptions();

        return view('faculty.ward_orientation.edit', [
            'orientation' => $orientation,
            'wardOptions' => $wardOptions,
        ]);
    }

    /**
     * Update orientation.
     */
    public function update(Request $request, WardOrientation $orientation)
    {
        $faculty = Auth::guard('faculty')->user();

        if ($orientation->created_by_faculty_id !== $faculty->id) {
            abort(403);
        }

        $data = $this->validateRequest($request);

        $action = $request->input('action', 'draft'); // 'draft' | 'publish'
        $status = $action === 'publish'
            ? WardOrientation::STATUS_PUBLISHED
            : WardOrientation::STATUS_DRAFT;

        // If title changed, optionally regenerate slug (simple approach)
        if ($orientation->title !== $data['title'] || $orientation->ward_code !== $data['ward_code']) {
            $slugBase = Str::slug($data['title'] . '-' . $data['ward_code']);
            $orientation->slug = $this->makeUniqueSlug($slugBase, $orientation->id);
        }

        $orientation->ward_code              = $data['ward_code'];
        $orientation->title                  = $data['title'];
        $orientation->summary                = $data['summary'] ?? null;
        $orientation->culture_text           = $data['culture_text'] ?? null;
        $orientation->routines_text          = $data['routines_text'] ?? null;
        $orientation->patient_cases_text     = $data['patient_cases_text'] ?? null;
        $orientation->workload_text          = $data['workload_text'] ?? null;
        $orientation->emergencies_text       = $data['emergencies_text'] ?? null;
        $orientation->layout_locations_text  = $data['layout_locations_text'] ?? null;
        $orientation->tips_text              = $data['tips_text'] ?? null;
        $orientation->estimated_watch_minutes= $data['estimated_watch_minutes'] ?? null;
        $orientation->status                 = $status;

        if ($status === WardOrientation::STATUS_PUBLISHED && !$orientation->published_at) {
            $orientation->published_at = now();
        }

        $orientation->save();

        return redirect()
            ->route('faculty.instructor.orientation.index')
            ->with('success', $status === WardOrientation::STATUS_PUBLISHED
                ? 'Ward orientation updated and published.'
                : 'Ward orientation updated as draft.');
    }

    /**
     * Archive (status only).
     */
    public function archive(WardOrientation $orientation)
    {
        $faculty = Auth::guard('faculty')->user();

        if ($orientation->created_by_faculty_id !== $faculty->id) {
            abort(403);
        }

        $orientation->status = WardOrientation::STATUS_ARCHIVED;
        $orientation->save();

        return redirect()
            ->route('faculty.instructor.orientation.index')
            ->with('success', 'Ward orientation archived.');
    }

    /**
     * Soft delete.
     */
    public function destroy(WardOrientation $orientation)
    {
        $faculty = Auth::guard('faculty')->user();

        if ($orientation->created_by_faculty_id !== $faculty->id) {
            abort(403);
        }

        $orientation->delete();

        return redirect()
            ->route('faculty.instructor.orientation.index')
            ->with('success', 'Ward orientation deleted.');
    }

    /**
     * Validation rules shared by store + update.
     */
    protected function validateRequest(Request $request): array
    {
        $wardCodes = array_keys($this->wardOptions()); // ['CHN','OB','DR',...]

        return $request->validate([
            'title'                   => ['required', 'string', 'max:190'],
            'ward_code'               => ['required', 'string', 'in:' . implode(',', $wardCodes)],
            'summary'                 => ['nullable', 'string'],
            'culture_text'            => ['nullable', 'string'],
            'routines_text'           => ['nullable', 'string'],
            'patient_cases_text'      => ['nullable', 'string'],
            'workload_text'           => ['nullable', 'string'],
            'emergencies_text'        => ['nullable', 'string'],
            'layout_locations_text'   => ['nullable', 'string'],
            'tips_text'               => ['nullable', 'string'],
            'estimated_watch_minutes' => ['nullable', 'integer', 'min:1', 'max:240'],
        ]);
    }

    /**
     * Ward options (code => label) – keep in sync with DB enum.
     */
    protected function wardOptions(): array
    {
        return [
            'CHN'      => 'Community Health Nursing',
            'OB'       => 'Obstetrics',
            'DR'       => 'Delivery Room',
            'PEDIA'    => 'Pediatrics',
            'MS'       => 'Medical-Surgical',
            'ICU'      => 'ICU',
            'ONCO'     => 'Oncology',
            'GERIA'    => 'Geriatric',
            'ORTHO'    => 'Orthopedics',
            'PSYCH'    => 'Psychiatric',
            'ER'       => 'Emergency Room',
            'OR'       => 'Operating Room',
            'MEDICINE' => 'Medicine Ward',
            'SURGERY'  => 'Surgery Ward',
            'DN'       => 'Dialysis / DN',
            'CDN'      => 'CDN',
        ];
    }

    /**
     * Generate a unique slug.
     */
    protected function makeUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = $base;
        $i = 2;

        while (
            WardOrientation::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
