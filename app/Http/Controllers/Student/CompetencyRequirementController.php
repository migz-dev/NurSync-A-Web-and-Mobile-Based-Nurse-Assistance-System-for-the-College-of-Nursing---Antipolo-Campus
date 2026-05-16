<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CompetencyItem;
use App\Models\CompetencyCategory;
use Illuminate\Http\Request;

class CompetencyRequirementController extends Controller
{
    /**
     * Student list view – published competencies only.
     */
    public function index(Request $request)
    {
        $q          = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category_id', 'all');

        $query = CompetencyItem::query()
            ->with('category')
            ->where('status', 'published'); // students see only published

        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($inner) use ($like) {
                $inner->where('title', 'like', $like)
                      ->orWhere('description', 'like', $like)
                      ->orWhere('reason', 'like', $like);
            });
        }

        if ($categoryId !== 'all' && $categoryId !== '' && $categoryId !== null) {
            $query->where('category_id', $categoryId);
        }

        $items      = $query->orderBy('title')->get();
        $categories = CompetencyCategory::orderBy('title')->get();

        $filters = [
            'q'           => $q,
            'category_id' => $categoryId,
        ];

        return view('student.competencies.index', [
            'items'      => $items,
            'categories' => $categories,
            'filters'    => $filters,
        ]);
    }

    /**
     * Student show view – single competency (must be published).
     */
    public function show(CompetencyItem $competency)
    {
        // Guard: students only see published competencies
        if ($competency->status !== 'published') {
            abort(404);
        }

        $competency->load(['category', 'explanations']);

        return view('student.competencies.show', [
            'item' => $competency,
        ]);
    }
}
