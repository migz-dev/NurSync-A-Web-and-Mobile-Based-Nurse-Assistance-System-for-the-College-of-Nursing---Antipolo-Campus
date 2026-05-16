<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssessmentGuide;

class AssessmentGuideController extends Controller
{
    // List published guides for students (view-only)
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $guides = AssessmentGuide::published()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(function ($inner) use ($like) {
                    $inner->where('title', 'like', $like)
                          ->orWhere('summary', 'like', $like)
                          ->orWhere('content_rubric', 'like', $like)
                          ->orWhere('content_documentation', 'like', $like)
                          ->orWhere('content_tips', 'like', $like)
                          ->orWhere('content_mistakes', 'like', $like);
                });
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        $filters = ['q' => $q];

        return view('student.assessment.index', compact('guides', 'filters'));
    }

    // View a single published guide (view-only)
    public function show(AssessmentGuide $assessmentGuide)
    {
        // Make sure students can only see published guides
        if (($assessmentGuide->status ?? 'draft') !== 'published') {
            abort(404);
        }

        return view('student.assessment.show', [
            'guide' => $assessmentGuide,
        ]);
    }
}
