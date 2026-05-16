<?php
// app/Http/Controllers/Faculty/DrugGuideController.php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    DrugProduct,
    DrugDosageForm,
    DrugPharmacologicCategory,
    DrugCompany
};

class DrugGuideController extends Controller
{
    /** Page shell + filters */
    public function index(Request $request)
    {
        // For dropdown filters on the CI Drug Guide page
        $forms = DrugDosageForm::orderBy('name')->get(['id', 'name']);
        $cats  = DrugPharmacologicCategory::orderBy('name')->get(['id', 'name']);
        $mfgs  = DrugCompany::orderBy('name')->get(['id', 'name']);

        // Distinct normalized packaging types & class groups from DrugProduct
        $packagingTypes = DrugProduct::query()
            ->whereNotNull('packaging_type')
            ->where('packaging_type', '!=', '')
            ->distinct()
            ->orderBy('packaging_type')
            ->pluck('packaging_type');

        $classGroups = DrugProduct::query()
            ->whereNotNull('class_group')
            ->where('class_group', '!=', '')
            ->distinct()
            ->orderBy('class_group')
            ->pluck('class_group');

        return view('faculty.drug_guide.index', [
            'forms'          => $forms,
            'cats'           => $cats,
            'mfgs'           => $mfgs,
            'packagingTypes' => $packagingTypes, // e.g. "Tablet (Blister Pack)", "IV Infusion / IV Bag"
            'classGroups'    => $classGroups,    // e.g. "Antibiotic", "Analgesic", etc.
        ]);
    }

    /**
     * JSON endpoint: list/search/paginate faculty drug guide
     * Query params:
     * - q
     * - form_id
     * - cat_id
     * - class        (legacy; mapped to class_group)
     * - drug_class   (preferred; mapped to class_group)
     * - packaging_type
     * - mfg_id
     * - sort: brand_name|registration_number|issued_at|expires_at|created_at
     * - dir: asc|desc
     * - page, per_page
     */
    public function data(Request $request)
    {
        $q             = trim((string) $request->query('q', ''));
        $formId        = (int) $request->query('form_id', 0);
        $catId         = (int) $request->query('cat_id', 0);
        $legacyClass   = trim((string) $request->query('class', ''));        // old param name
        $drugClass     = trim((string) $request->query('drug_class', $legacyClass)); // preferred param
        $packagingType = trim((string) $request->query('packaging_type', ''));
        $mfgId         = (int) $request->query('mfg_id', 0);

        $perPage  = min(36, max(6, (int) $request->query('per_page', 12)));
        $page     = max(1, (int) $request->query('page', 1));

        $sortable = ['brand_name', 'registration_number', 'issued_at', 'expires_at', 'created_at'];
        $sort     = $request->query('sort', 'brand_name');
        if (!in_array($sort, $sortable, true)) {
            $sort = 'brand_name';
        }
        $dir = strtolower($request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $base = DrugProduct::query()
            ->select([
                'id',
                'brand_name',
                'registration_number',
                'substance_id',
                'dosage_form_id',
                'pharmacologic_category_id',
                'manufacturer_id',
                'dosage_strength',
                'classification',   // raw (e.g. OTC, Rx)
                'class_group',      // normalized class (Antibiotic, etc.)
                'packaging',
                'packaging_type',   // normalized packaging (Tablet, IV bag, etc.)
                'issued_at',
                'expires_at',
            ])
            ->with([
                'substance:id,name',
                'dosageForm:id,name',
                'category:id,name',
                'manufacturer:id,name',
            ])
            ->when($q !== '', function ($w) use ($q) {
                $needle = "%{$q}%";
                $w->where(function ($s) use ($needle) {
                    $s->where('brand_name', 'like', $needle)
                      ->orWhere('registration_number', 'like', $needle)
                      ->orWhere('dosage_strength', 'like', $needle)
                      ->orWhereHas('substance', fn ($x) => $x->where('name', 'like', $needle))
                      ->orWhereHas('manufacturer', fn ($m) => $m->where('name', 'like', $needle));
                });
            })
            ->when($formId, fn ($w) => $w->where('dosage_form_id', $formId))
            ->when($catId,  fn ($w) => $w->where('pharmacologic_category_id', $catId))
            // normalized class filter (preferred)
            ->when($drugClass !== '', fn ($w) => $w->where('class_group', $drugClass))
            // packaging type filter
            ->when($packagingType !== '', fn ($w) => $w->where('packaging_type', $packagingType))
            ->when($mfgId, fn ($w) => $w->where('manufacturer_id', $mfgId))
            ->orderBy($sort, $dir);

        // Length-aware pagination (works great for AJAX)
        $paginator = $base->paginate($perPage, ['*'], 'page', $page)->withQueryString();

        $items = $paginator->getCollection()->map(function (DrugProduct $p) {
            return [
                'id'             => $p->id,
                'brand'          => $p->brand_name,
                'generic'        => optional($p->substance)->name,
                'strength'       => $p->dosage_strength,
                'form'           => optional($p->dosageForm)->name,
                'category'       => optional($p->category)->name,
                'reg_no'         => $p->registration_number,
                // For backward compatibility, 'class' returns normalized class if present
                'class'          => $p->class_group ?: $p->classification,
                'raw_class'      => $p->classification,
                'drug_class'     => $p->class_group,
                'packaging'      => $p->packaging,
                'packaging_type' => $p->packaging_type,
                'mfg'            => optional($p->manufacturer)->name,
                'issued_at'      => optional($p->issued_at)?->toDateString(),
                'expires_at'     => optional($p->expires_at)?->toDateString(),
                'has_demo'       => false,
                'has_pdf'        => false,
                'show_url'       => route('faculty.drug_guide.show', $p->id),
            ];
        })->values();

        return response()->json([
            'page'      => $paginator->currentPage(),
            'per_page'  => $paginator->perPage(),
            'total'     => $paginator->total(),
            'items'     => $items,
        ]);
    }

    /** Route-model bound detail page */
    public function show($id)
    {
        $product = DrugProduct::with([
            'substance:id,name',
            'dosageForm:id,name',
            'category:id,name',
            'manufacturer:id,name,country',
            'importer:id,name,country',
            'distributor:id,name,country',
            'trader:id,name,country',
        ])->findOrFail($id);

        return view('faculty.drug_guide.show', compact('product'));
    }
}