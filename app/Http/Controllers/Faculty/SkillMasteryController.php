<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\SkillMasteryChecklist;
use App\Models\SkillMasteryStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SkillMasteryController extends Controller
{
    /**
     * List all skill mastery checklists for the logged-in CI.
     */
    public function index()
    {
        $ci = Auth::guard('faculty')->user();

        // Eager-load relations for counts on index cards
        $checklists = SkillMasteryChecklist::with(['steps', 'equipment', 'tags'])
            ->where('faculty_id', $ci->id)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        return view('faculty.instructor.skills.index', [
            'checklists' => $checklists,
        ]);
    }

    /**
     * Show the "create new checklist" form.
     */
    public function create()
    {
        [$categories, $areas] = $this->metaOptions();

        return view('faculty.instructor.skills.create', [
            'categories' => $categories,
            'areas'      => $areas,
        ]);
    }

    /**
     * Store a newly created skill mastery checklist.
     */
    public function store(Request $request)
    {
        $ci = Auth::guard('faculty')->user();
        [$categories, $areas] = $this->metaOptions();

        $validated = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'category'       => ['required', 'string', Rule::in($categories)],
            'skill_area'     => ['nullable', 'string', Rule::in($areas)],
            'summary'        => ['nullable', 'string'],
            'pre_procedure'  => ['nullable', 'string'],
            'post_procedure' => ['nullable', 'string'],
            'safety_notes'   => ['nullable', 'string'],
            'status'         => ['required', 'string', Rule::in(['draft', 'published'])],
        ]);

        $validated['faculty_id'] = $ci->id;

        $checklist = SkillMasteryChecklist::create($validated);

        return redirect()
            ->route('faculty.instructor.skills.edit', $checklist->slug)
            ->with('success', 'Skill mastery checklist created. You can now add steps and refine the content.');
    }

    /**
     * Show a single checklist (CI view).
     */
    public function show(string $slug)
    {
        $ci = Auth::guard('faculty')->user();

        $checklist = SkillMasteryChecklist::with(['steps', 'equipment', 'tags'])
            ->where('faculty_id', $ci->id)
            ->where('slug', $slug)
            ->firstOrFail();

        return view('faculty.instructor.skills.show', [
            'checklist' => $checklist,
        ]);
    }

    /**
     * Show the edit form for a checklist.
     */
    public function edit(string $slug)
    {
        $ci = Auth::guard('faculty')->user();
        [$categories, $areas] = $this->metaOptions();

        $checklist = SkillMasteryChecklist::with(['steps', 'equipment', 'tags'])
            ->where('faculty_id', $ci->id)
            ->where('slug', $slug)
            ->firstOrFail();

        return view('faculty.instructor.skills.edit', [
            'checklist'  => $checklist,
            'categories' => $categories,
            'areas'      => $areas,
        ]);
    }

    /**
     * Update an existing checklist.
     */
    public function update(Request $request, string $slug)
    {
        $ci = Auth::guard('faculty')->user();
        [$categories, $areas] = $this->metaOptions();

        $checklist = SkillMasteryChecklist::where('faculty_id', $ci->id)
            ->where('slug', $slug)
            ->firstOrFail();

        $validated = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'category'       => ['required', 'string', Rule::in($categories)],
            'skill_area'     => ['nullable', 'string', Rule::in($areas)],
            'summary'        => ['nullable', 'string'],
            'pre_procedure'  => ['nullable', 'string'],
            'post_procedure' => ['nullable', 'string'],
            'safety_notes'   => ['nullable', 'string'],
            'status'         => ['required', 'string', Rule::in(['draft', 'published', 'archived'])],
        ]);

        $checklist->update($validated);

        return redirect()
            ->route('faculty.instructor.skills.edit', $checklist->slug)
            ->with('success', 'Skill mastery checklist updated successfully.');
    }

    /**
     * Archive a checklist (soft archive via status).
     */
    public function archive(string $slug)
    {
        $ci = Auth::guard('faculty')->user();

        $checklist = SkillMasteryChecklist::where('faculty_id', $ci->id)
            ->where('slug', $slug)
            ->firstOrFail();

        $checklist->update([
            'status' => 'archived',
        ]);

        return redirect()
            ->route('faculty.instructor.skills.index')
            ->with('success', 'Checklist archived. It will no longer appear in your main list.');
    }

    /**
     * Store a new step for the given checklist (from Edit page).
     */
    public function storeStep(Request $request, string $slug)
    {
        $ci = Auth::guard('faculty')->user();

        // Ensure CI owns this checklist
        $checklist = SkillMasteryChecklist::where('faculty_id', $ci->id)
            ->where('slug', $slug)
            ->firstOrFail();

        $validated = $request->validate([
            'step_no'      => ['required', 'integer', 'min:1'],
            'action'       => ['required', 'string'],
            'rationale'    => ['nullable', 'string'],
            'safety_point' => ['nullable', 'string'],
        ]);

        $validated['checklist_id'] = $checklist->id;

        SkillMasteryStep::create($validated);

        return redirect()
            ->route('faculty.instructor.skills.edit', $checklist->slug)
            ->with('success', 'New step added to the checklist.');
    }

    /**
     * Shared dropdown options for categories & areas.
     *
     * @return array [categories, areas]
     */
    protected function metaOptions(): array
    {
        // Skill categories shown in forms (you can adjust anytime)
        $categories = [
            'Vital Signs',
            'Medication Administration',
            'IV Therapy',
            'Wound Care',
            'Catheterization',
            'Tube Feeding',
            'OR / DR Skills',
            'ICU Routines',
            'Emergency / ER Skills',
            'Assessment & Monitoring',
            'Other',
        ];

        // Clinical areas (mirrors your wards list)
        $areas = [
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

        return [$categories, $areas];
    }
}
