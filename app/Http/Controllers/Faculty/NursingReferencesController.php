<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\NursingReference;
use Illuminate\Http\Request;

class NursingReferencesController extends Controller
{
    /**
     * Display the Nursing References library.
     */
    public function index(Request $request)
    {
        $q        = trim((string) $request->input('q', ''));
        $category = $request->input('category');
        $source   = $request->input('source');
        $perPage  = (int) $request->input('per_page', 12);
        if ($perPage <= 0 || $perPage > 100) {
            $perPage = 12;
        }

        $query = NursingReference::query()->where('is_active', true);

        // Full-text filters
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('source', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('url', 'like', "%{$q}%");
            });
        }

        if (!empty($category)) {
            $query->where('category', $category);
        }

        if (!empty($source)) {
            $query->where('source', $source);
        }

        $query->orderBy('is_featured', 'desc')->orderBy('title');
        $items = $query->paginate($perPage)->appends($request->query());

        // Filter options
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

        // AJAX (for client-side search/filter)
        if ($request->ajax()) {
            $rows = view('faculty.nursing_references._cards', compact('items'))->render();
            $pager = view('faculty.nursing_references._pager', compact('items'))->render();

            return response()->json([
                'rows'  => $rows,
                'pager' => $pager,
            ]);
        }

        // Full page render
        return view('faculty.nursing_references.index', compact('items', 'categories', 'sources'));
    }
}
