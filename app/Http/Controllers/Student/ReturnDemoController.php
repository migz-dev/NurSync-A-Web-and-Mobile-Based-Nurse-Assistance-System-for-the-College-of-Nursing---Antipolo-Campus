<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ReturnDemoSkill;
use Illuminate\Http\Request;

class ReturnDemoController extends Controller
{
// app/Http/Controllers/Student/ReturnDemoController.php
public function index(Request $request)
{
    $q    = trim((string) $request->get('q', ''));
    $ward = (string) $request->get('ward', 'all');

    $skills = ReturnDemoSkill::query()
        ->visible()
        ->search($q)
        ->ward($ward)
        ->withCount('steps')
        ->orderBy('title')
        ->paginate(12)
        ->withQueryString();

    $wards = ReturnDemoSkill::query()
        ->visible()
        ->select('clinical_wards')
        ->whereNotNull('clinical_wards')
        ->distinct()
        ->orderBy('clinical_wards')
        ->pluck('clinical_wards')
        ->all();

    // ---------- AJAX fragment response ----------
    if ($request->ajax()) {
        return response()->json([
            'list'    => view('student.return-demo._cards', compact('skills'))->render(),
            'pager'   => view('student.return-demo._pager', compact('skills'))->render(),
            'summary' => $skills->total()
                ? "Showing {$skills->firstItem()}–{$skills->lastItem()} of {$skills->total()} procedures"
                : "No procedures found",
        ]);
    }

    return view('student.return-demo.index', compact('skills','wards','q','ward'));
}


    public function show(ReturnDemoSkill $skill)
    {
        // Hide archived or unpublished in production
        $designMode = (bool) config('app.design_mode', true);
        if ($skill->is_archived || (!$designMode && $skill->status !== 'published')) {
            abort(404);
        }

        $skill->load([
            'steps' => fn ($q) => $q->orderBy('step_no'),
            'attachments',
        ]);

        return view('student.return-demo.show', compact('skill'));
    }
}
