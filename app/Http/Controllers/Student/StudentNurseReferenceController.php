<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\NursingReference;
use Illuminate\Http\Request;

class StudentNurseReferenceController extends Controller
{
    /**
     * Display a listing of only featured & active nursing references for Student Nurses.
     */
    public function index(Request $request)
    {
        $q        = trim((string) $request->input('q', ''));
        $category = $request->input('category');
        $source   = $request->input('source');
        $perPage  = (int) $request->input('per_page', 9);
        if ($perPage <= 0 || $perPage > 100) {
            $perPage = 9;
        }

        $query = NursingReference::query()
            ->where('is_active', true)
            ->where('is_featured', true);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('url', 'like', "%{$q}%")
                    ->orWhere('source', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if (!empty($category)) {
            $query->where('category', $category);
        }

        if (!empty($source)) {
            $query->where('source', $source);
        }

        $query->orderBy('title');

        $items = $query->paginate($perPage)->appends($request->query());

        // Filter lists
        $categories = NursingReference::query()
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $sources = NursingReference::query()
            ->select('source')
            ->whereNotNull('source')
            ->where('source', '!=', '')
            ->distinct()
            ->orderBy('source')
            ->pluck('source');

        // AJAX support (for live filtering)
        if ($request->ajax()) {
            $rows  = view('student.nursing_references._cards', compact('items'))->render();
            $pager = view('student.nursing_references._pager', compact('items'))->render();

            return response()->json([
                'rows'  => $rows,
                'pager' => $pager,
            ]);
        }

        // Render student-facing Nursing References page
        return view('student.nursing_references.index', compact('items', 'categories', 'sources'));
    }
}
