<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClinicalExperience;
use Illuminate\Http\Request;

class StudentClinicalExperienceController extends Controller
{
    /**
     * List published clinical experiences for Student Nurses.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('q', ''));
        $ward   = $request->input('ward');
        $sort   = $request->input('sort', 'recent'); // 'recent' | 'oldest'

        $query = ClinicalExperience::query()
            ->where('status', 'published')
            ->with([
                // Just load the relation; no explicit column list
                'faculty',
                'attachments' => function ($q) {
                    $q->orderByDesc('is_primary')
                      ->orderBy('sort_order')
                      ->orderBy('id');
                },
            ])
            ->withCount('attachments');

        // Search
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('story', 'like', "%{$search}%");
            });
        }

        // Ward filter
        if (!empty($ward)) {
            $query->where('ward', $ward);
        }

        // Sort order
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc'); // recent first
        }

        $experiences = $query->paginate(12)->withQueryString();

        return view('student.clinical_experiences.index', [
            'experiences' => $experiences,
            'search'      => $search,
            'ward'        => $ward,
            'sort'        => $sort,
        ]);
    }

    /**
     * Show a single published clinical experience to students.
     */
    public function show(ClinicalExperience $experience)
    {
        if ($experience->status !== 'published') {
            abort(404);
        }

        $experience->load([
            'faculty',   // no explicit column list
            'attachments' => function ($q) {
                $q->orderByDesc('is_primary')
                  ->orderBy('sort_order')
                  ->orderBy('id');
            },
        ]);

        return view('student.clinical_experiences.show', [
            'experience' => $experience,
        ]);
    }
}
