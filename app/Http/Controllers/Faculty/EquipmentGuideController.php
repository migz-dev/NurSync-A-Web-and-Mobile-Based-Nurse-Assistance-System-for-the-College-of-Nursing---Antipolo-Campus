<?php
// app/Http/Controllers/Faculty/EquipmentGuideController.php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\EquipmentGuide;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EquipmentGuideController extends Controller
{
    public function index(Request $request): View
    {
        // Distinct values for the filter selects
        $categories = EquipmentGuide::query()
            ->select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category')->toArray();

        $wards = EquipmentGuide::query()
            ->select('ward_scope')->whereNotNull('ward_scope')->distinct()->orderBy('ward_scope')->pluck('ward_scope')->toArray();

        return view('faculty.equipment-guides/index', compact('categories', 'wards'));
    }

    public function data(Request $request): JsonResponse
    {
        $q        = trim((string) $request->query('q', ''));
        $category = (string) $request->query('category', '');
        $ward     = (string) $request->query('ward', '');
        $page     = max(1, (int) $request->query('page', 1));
        $perPage  = max(1, min(50, (int) $request->query('per_page', 12))); // cap at 50

        $query = EquipmentGuide::query()
            ->when($q !== '', function (Builder $sql) use ($q) {
                $like = '%' . $q . '%';
                $sql->where(function (Builder $w) use ($like) {
                    $w->where('item_name', 'like', $like)
                      ->orWhere('typical_uses', 'like', $like)
                      ->orWhere('notes', 'like', $like)
                      ->orWhere('variants_or_examples', 'like', $like)
                      ->orWhere('related_procedures_or_tasks', 'like', $like);
                });
            })
            ->when($category !== '', fn (Builder $sql) => $sql->where('category', $category))
            ->when($ward !== '',     fn (Builder $sql) => $sql->where('ward_scope', $ward));

        $total = (clone $query)->count();

        $rows = $query
            ->orderBy('category')
            ->orderBy('ward_scope')
            ->orderBy('item_name')
            ->forPage($page, $perPage)
            ->get([
                'id',
                'item_name',
                'category',
                'ward_scope',
                'variants_or_examples',
                'typical_uses',
                'related_procedures_or_tasks',
                'notes',
            ]);

        // Map DB columns → frontend fields
        $items = $rows->map(function (EquipmentGuide $it) {
            // Optional: derive a single related slug if you want to link to a procedure
            $firstRelated = '';
            if (!empty($it->related_procedures_or_tasks)) {
                $firstRelated = trim(explode(',', $it->related_procedures_or_tasks)[0] ?? '');
            }

            return [
                'id'        => $it->id,
                'item_name' => $it->item_name,
                'category'  => $it->category,
                'ward'      => $it->ward_scope,
                'variants'  => $it->variants_or_examples,
                'uses'      => $it->typical_uses,
                'related'   => $it->related_procedures_or_tasks,
                'notes'     => $it->notes,
                // If you have a procedure show route, you can wire it here.
                // 'show_url'  => $firstRelated && \Route::has('student.procedures.show')
                //                 ? route('student.procedures.show', ['slug' => $firstRelated])
                //                 : null,
                'show_url'  => null,
            ];
        })->values();

        return response()->json([
            'page'     => $page,
            'per_page' => $perPage,
            'total'    => $total,
            'items'    => $items,
        ]);
    }
}