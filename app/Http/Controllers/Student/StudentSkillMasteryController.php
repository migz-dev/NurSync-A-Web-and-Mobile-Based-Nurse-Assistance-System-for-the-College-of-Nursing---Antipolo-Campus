<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SkillMasteryChecklist;

class StudentSkillMasteryController extends Controller
{
    /**
     * List all PUBLISHED checklists for students.
     */
    public function index()
    {
        $checklists = SkillMasteryChecklist::with(['steps', 'equipment', 'tags'])
            ->published()            // <--- Only published items
            ->orderBy('category')
            ->orderBy('title')
            ->get();

        return view('student.skills.index', [
            'checklists' => $checklists,
            'active'     => 'skill_checklists',
        ]);
    }

    /**
     * Show a single checklist (VIEW-ONLY).
     */
    public function show($slug)
    {
        $checklist = SkillMasteryChecklist::with(['steps', 'equipment', 'tags'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('student.skills.show', [
            'checklist' => $checklist,
            'active'     => 'skill_checklists',
        ]);
    }
}
