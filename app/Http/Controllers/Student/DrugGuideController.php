<?php

namespace App\Http\Controllers\Student;

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
    /** Page shell + filters (Student) */
public function index(Request $request)
{
    return view('student.drug_guide.index', [
        'forms'          => DrugDosageForm::orderBy('name')->get(['id','name']),
        'cats'           => DrugPharmacologicCategory::orderBy('name')->get(['id','name']),
        'mfgs'           => DrugCompany::orderBy('name')->get(['id','name']),
        'packagingTypes' => DrugProduct::query()
                            ->whereNotNull('packaging_type')
                            ->where('packaging_type','!=','')
                            ->distinct()
                            ->orderBy('packaging_type')
                            ->pluck('packaging_type'),
        'classGroups'    => DrugProduct::query()
                            ->whereNotNull('class_group')
                            ->where('class_group','!=','')
                            ->distinct()
                            ->orderBy('class_group')
                            ->pluck('class_group'),
    ]);
}


    /**
     * JSON endpoint for Student Drug Guide
     * Query params:
     * q, form_id, cat_id, drug_class, packaging_type, sort, dir, page, per_page
     */
    public function data(Request $request)
    {
        $q             = trim((string)$request->query('q', ''));
        $formId        = (int)$request->query('form_id', 0);
        $catId         = (int)$request->query('cat_id', 0);
        $drugClass     = trim((string)$request->query('drug_class', ''));
        $packagingType = trim((string)$request->query('packaging_type', ''));

        $perPage  = min(36, max(6, (int)$request->query('per_page', 12)));
        $page     = max(1, (int)$request->query('page', 1));

        $sortable = ['brand_name','registration_number','issued_at','expires_at','created_at'];
        $sort     = $request->query('sort', 'brand_name');
        if (!in_array($sort, $sortable, true)) {
            $sort = 'brand_name';
        }
        $dir = strtolower($request->query('dir','asc')) === 'desc' ? 'desc' : 'asc';

        $base = DrugProduct::query()
            ->select([
                'id','brand_name','registration_number','substance_id','dosage_form_id',
                'pharmacologic_category_id','manufacturer_id','dosage_strength',
                'classification','class_group','packaging_type','packaging',
                'issued_at','expires_at'
            ])
            ->with([
                'substance:id,name',
                'dosageForm:id,name',
                'category:id,name',
                'manufacturer:id,name',
            ])
            ->when($q !== '', function ($w) use ($q) {
                $needle = "%{$q}%";
                $w->where(function($s) use ($needle) {
                    $s->where('brand_name', 'like', $needle)
                      ->orWhere('registration_number', 'like', $needle)
                      ->orWhere('dosage_strength', 'like', $needle)
                      ->orWhereHas('substance', fn($x)=>$x->where('name','like',$needle))
                      ->orWhereHas('manufacturer', fn($m)=>$m->where('name','like',$needle));
                });
            })
            ->when($formId, fn($w)=>$w->where('dosage_form_id', $formId))
            ->when($catId,  fn($w)=>$w->where('pharmacologic_category_id', $catId))

            // NEW: normalized drug class filter
            ->when($drugClass !== '', fn($w)=>$w->where('class_group', $drugClass))

            // NEW: packaging-type filter
            ->when($packagingType !== '', fn($w)=>$w->where('packaging_type', $packagingType))

            ->orderBy($sort, $dir);

        $paginator = $base->paginate($perPage, ['*'], 'page', $page)->withQueryString();

        $items = $paginator->getCollection()->map(function($p){
            return [
                'id'             => $p->id,
                'brand'          => $p->brand_name,
                'generic'        => optional($p->substance)->name,
                'strength'       => $p->dosage_strength,
                'form'           => optional($p->dosageForm)->name,
                'category'       => optional($p->category)->name,
                
                // NEW OUTPUTS:
                'drug_class'     => $p->class_group,   // normalized class
                'raw_class'      => $p->classification, // original FDA class
                'packaging_type' => $p->packaging_type,
                'packaging'      => $p->packaging,

                'reg_no'         => $p->registration_number,
                'mfg'            => optional($p->manufacturer)->name,
                'issued_at'      => optional($p->issued_at)?->toDateString(),
                'expires_at'     => optional($p->expires_at)?->toDateString(),

                'has_demo'       => false,
                'has_pdf'        => false,

                'show_url'       => route('student.drugs.show', $p->id),
            ];
        })->values();

        return response()->json([
            'page'      => $paginator->currentPage(),
            'per_page'  => $paginator->perPage(),
            'total'     => $paginator->total(),
            'items'     => $items,
        ]);
    }

    /** Detail page for a single drug (Student view) */
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

        return view('student.drug_guide.show', compact('product'));
    }
}
