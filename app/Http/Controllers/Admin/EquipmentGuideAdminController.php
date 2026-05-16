<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EquipmentGuide;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipmentGuideAdminController extends Controller
{
    public function __construct()
    {
        // Protect with your admin guard/middleware if you have one
        // $this->middleware(['auth:admin']);
    }

    /**
     * List equipment with filters & pagination.
     * Query params: q, category, ward, per_page
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', ''));
        $ward = trim((string) $request->query('ward', ''));
        $perPage = (int) $request->query('per_page', 7); // ⬅ per-page default 11

        $items = \App\Models\EquipmentGuide::query()
            ->when($q !== '', function ($qry) use ($q) {
                $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $q) . '%';
                $qry->where(function ($w) use ($like) {
                    $w->where('item_name', 'like', $like)
                        ->orWhere('typical_uses', 'like', $like)
                        ->orWhere('related_procedures_or_tasks', 'like', $like)
                        ->orWhere('notes', 'like', $like);
                });
            })
            ->when($category !== '', fn($qry) => $qry->where('category', $category))
            ->when($ward !== '', fn($qry) => $qry->where('ward_scope', $ward))
            ->orderBy('category')->orderBy('ward_scope')->orderBy('item_name')
            ->paginate(max(1, min($perPage, 200)))
            ->withQueryString();

        // For filters
        $categories = \App\Models\EquipmentGuide::query()->select('category')->distinct()->orderBy('category')->pluck('category');
        $wards = \App\Models\EquipmentGuide::query()->select('ward_scope')->distinct()->orderBy('ward_scope')->pluck('ward_scope');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'rows' => view('admin.equipment_guides._rows', compact('items'))->render(),
                'pager' => view('admin.equipment_guides._pager', compact('items'))->render(),
            ]);
        }

        return view('admin.equipment_guides.index', compact('items', 'categories', 'wards'));
    }


    /** Show create form */
    public function create()
    {
        $categories = \App\Models\EquipmentGuide::query()
            ->select('category')->distinct()->orderBy('category')->pluck('category');
        $wards = \App\Models\EquipmentGuide::query()
            ->select('ward_scope')->distinct()->orderBy('ward_scope')->pluck('ward_scope');

        return view('admin.equipment_guides.create', compact('categories', 'wards'));
    }


    /** Persist new equipment item */
    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $item = EquipmentGuide::create($data);

        return redirect()
            ->route('admin.equipment_guide.show', $item->id)
            ->with('success', 'Equipment item created successfully.');
    }

    /** Show a single item */
    public function show(int $equipment)
    {
        $item = EquipmentGuide::findOrFail($equipment);

        return view('admin.equipment_guides.show', compact('item'));
    }

    /** Show edit form */
public function edit(int $equipment)
{
    $item = \App\Models\EquipmentGuide::findOrFail($equipment);

    $categories = \App\Models\EquipmentGuide::query()
        ->select('category')->distinct()->orderBy('category')->pluck('category');
    $wards = \App\Models\EquipmentGuide::query()
        ->select('ward_scope')->distinct()->orderBy('ward_scope')->pluck('ward_scope');

    return view('admin.equipment_guides.edit', compact('item', 'categories', 'wards'));
}

    /** Update an item */
    public function update(Request $request, int $equipment)
    {
        $item = EquipmentGuide::findOrFail($equipment);

        $data = $this->validateData($request, updating: true);

        $item->update($data);

        return redirect()
            ->route('admin.equipment_guide.show', $item->id)
            ->with('success', 'Equipment item updated successfully.');
    }

    /** Delete an item */
    public function destroy(int $equipment)
    {
        $item = EquipmentGuide::findOrFail($equipment);
        $item->delete();

        return redirect()
            ->route('admin.equipment_guide.index')
            ->with('success', 'Equipment item deleted.');
    }

    /**
     * Centralized validation rules.
     * Matches your columns: category, ward_scope, item_name, variants_or_examples,
     * typical_uses, related_procedures_or_tasks, notes
     */
    protected function validateData(Request $request, bool $updating = false): array
    {
        return $request->validate([
            'category' => ['nullable', 'string', 'max:100'],
            'ward_scope' => ['nullable', 'string', 'max:100'],
            'item_name' => ['required', 'string', 'max:255'],
            'variants_or_examples' => ['nullable', 'string'],
            'typical_uses' => ['nullable', 'string'],
            'related_procedures_or_tasks' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ], [
            'item_name.required' => 'Please provide the equipment name.',
        ]);
    }
}
