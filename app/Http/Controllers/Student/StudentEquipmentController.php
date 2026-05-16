<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EquipmentGuide;

class StudentEquipmentController extends Controller
{
    /**
     * List page (Student Equipment Guides index).
     */
    public function index(Request $request)
    {
        // Build distinct lists for filters (optional; falls back to UI defaults if empty)
        $categories = EquipmentGuide::query()
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->toArray();

        // NOTE: use ward_scope (real column) instead of ward
        $wards = EquipmentGuide::query()
            ->select('ward_scope')
            ->whereNotNull('ward_scope')
            ->where('ward_scope', '!=', '')
            ->distinct()
            ->orderBy('ward_scope')
            ->pluck('ward_scope')
            ->toArray();

        return view('student.equipment.index', [
            'categories' => $categories,
            'wards'      => $wards,
        ]);
    }

    /**
     * JSON data endpoint for the student Equipment Guides grid.
     * Read-only for students.
     */
    public function data(Request $request)
    {
        $perPage = (int) $request->input('per_page', 12);
        $perPage = max(1, min(50, $perPage));

        $page = (int) $request->input('page', 1);
        $page = max(1, $page);

        $q        = trim((string) $request->input('q', ''));
        $category = $request->input('category');
        $ward     = $request->input('ward');

        $query = EquipmentGuide::query();
        // If you later add a `status` column, you can uncomment this:
        // ->where('status', 'published');

        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $like = '%' . $q . '%';

                $builder->where('item_name', 'like', $like)
                    ->orWhere('typical_uses', 'like', $like)
                    ->orWhere('notes', 'like', $like)
                    ->orWhere('variants_or_examples', 'like', $like)
                    ->orWhere('related_procedures_or_tasks', 'like', $like);
            });
        }

        if (!empty($category)) {
            $query->where('category', $category);
        }

        if (!empty($ward)) {
            // Use ward_scope (real column) and still allow “global” rows
            $query->where(function ($builder) use ($ward) {
                $builder->where('ward_scope', $ward)
                    ->orWhereNull('ward_scope')
                    ->orWhere('ward_scope', '')
                    ->orWhere('ward_scope', 'All wards');
            });
        }

        $total = $query->count();

        $items = $query
            ->orderBy('category')
            ->orderBy('item_name')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Map DB columns → front-end keys expected by JS (cardTemplate)
        $items = $items->map(function (EquipmentGuide $equipment) {
            return [
                'id'        => $equipment->id,
                'item_name' => $equipment->item_name,
                'category'  => $equipment->category,
                'ward'      => $equipment->ward_scope ?: 'All wards',
                'variants'  => $equipment->variants_or_examples,
                'uses'      => $equipment->typical_uses,
                'related'   => $equipment->related_procedures_or_tasks,
                'notes'     => $equipment->notes,
                'show_url'  => route('student.equipment.show', $equipment->id),
            ];
        });

        return response()->json([
            'page'     => $page,
            'per_page' => $perPage,
            'total'    => $total,
            'items'    => $items,
        ]);
    }

    /**
     * Show a single Equipment Guide (read-only for students).
     */
    public function show($id)
    {
        $query = EquipmentGuide::query();
        // If you add a `status` column later, you can filter here too:
        // ->where('status', 'published');

        $equipment = $query
            ->where('id', $id)
            ->firstOrFail();

        return view('student.equipment.show', [
            'equipment' => $equipment,
        ]);
    }
}
