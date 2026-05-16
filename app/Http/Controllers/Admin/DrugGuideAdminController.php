<?php
// app/Http/Controllers/Admin/DrugGuideAdminController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\{
    DrugProduct,
    DrugSubstance,
    DrugDosageForm,
    DrugPharmacologicCategory,
    DrugCompany
};

class DrugGuideAdminController extends Controller
{
    /**
     * List drug products with search, filters, sorting, pagination.
     * Supports full-page render and AJAX (JSON with partial HTML).
     */
    public function index(Request $request)
    {
        // -------- Inputs & sane defaults --------
        $q    = trim((string) $request->get('q', ''));
        $form = (string) $request->get('form', '');
        $cat  = (string) $request->get('cat', '');
        $mfg  = (string) $request->get('mfg', '');

        // Normalized filters (classification + packaging)
        $drugClass     = trim((string) $request->get('drug_class', ''));
        $packagingType = trim((string) $request->get('packaging_type', ''));

        // Per-page: clamp 5..100 (default 10)
        $per = (int) $request->get('per', 10);
        if ($per < 5 || $per > 100) {
            $per = 10;
        }

        // Sort whitelist + direction fallback
        $sortable = ['brand_name', 'registration_number', 'issued_at', 'expires_at', 'created_at'];
        $sortIn   = (string) $request->get('sort', 'brand_name');
        $sort     = in_array($sortIn, $sortable, true) ? $sortIn : 'brand_name';

        $dirIn = strtolower((string) $request->get('dir', 'asc'));
        $dir   = $dirIn === 'desc' ? 'desc' : 'asc';

        // -------- Base query --------
        $products = DrugProduct::query()
            ->select([
                'id',
                'brand_name',
                'registration_number',
                'substance_id',
                'dosage_form_id',
                'pharmacologic_category_id',
                'manufacturer_id',
                'dosage_strength',
                'classification',
                'packaging',
                'issued_at',
                'expires_at',
                'country_of_origin',
                'application_type',
                'created_at',
            ])
            ->with([
                'substance:id,name',
                'dosageForm:id,name',
                'category:id,name',
                'manufacturer:id,name,country',
            ])

            // Search
            ->when($q !== '', function ($query) use ($q) {
                $needle = "%{$q}%";
                $query->where(function ($qq) use ($needle) {
                    $qq->where('brand_name', 'like', $needle)
                        ->orWhere('registration_number', 'like', $needle)
                        ->orWhere('dosage_strength', 'like', $needle)
                        ->orWhereHas('substance', fn ($s) => $s->where('name', 'like', $needle))
                        ->orWhereHas('manufacturer', fn ($m) => $m->where('name', 'like', $needle));
                });
            })

            // Standard filters
            ->when($form !== '', fn ($qq) => $qq->where('dosage_form_id', $form))
            ->when($cat  !== '', fn ($qq) => $qq->where('pharmacologic_category_id', $cat))
            ->when($mfg  !== '', fn ($qq) => $qq->where('manufacturer_id', $mfg))

            // Normalized drug class filter (classification)
            ->when($drugClass !== '', function ($qq) use ($drugClass) {
                $needle = strtolower($drugClass);
                $qq->whereRaw('LOWER(TRIM(classification)) = ?', [$needle]);
            })

            // Normalized packaging type filter (first word of packaging)
            ->when($packagingType !== '', function ($qq) use ($packagingType) {
                $needle = strtolower($packagingType);
                $qq->whereRaw(
                    "LOWER(TRIM(SUBSTRING_INDEX(packaging, ' ', 1))) = ?",
                    [$needle]
                );
            })

            ->orderBy($sort, $dir)
            ->paginate($per)
            ->withQueryString();

        // -------- AJAX partials (for JS fetchList) --------
        if ($request->wantsJson()) {
            return response()->json([
                'rows'    => view('admin.drug_guide._rows',    ['products' => $products])->render(),
                'pager'   => view('admin.drug_guide._pager',   ['products' => $products])->render(),
                'summary' => view('admin.drug_guide._summary', ['products' => $products])->render(),
            ]);
        }

        // -------- Dropdown data --------
        $forms = DrugDosageForm::orderBy('name')->get(['id', 'name']);
        $cats  = DrugPharmacologicCategory::orderBy('name')->get(['id', 'name']);
        $mfgs  = DrugCompany::orderBy('name')->get(['id', 'name']);

        // Normalized class options
        $classGroups = DrugProduct::query()
            ->whereNotNull('classification')
            ->where('classification', '!=', '')
            ->selectRaw('LOWER(TRIM(classification)) as g')
            ->groupBy('g')
            ->orderBy('g')
            ->pluck('g')
            ->map(fn ($g) => ucwords($g))
            ->values()
            ->all();

        // Normalized packaging options (first word)
        $packagingTypes = DrugProduct::query()
            ->whereNotNull('packaging')
            ->where('packaging', '!=', '')
            ->selectRaw("LOWER(TRIM(SUBSTRING_INDEX(packaging, ' ', 1))) as p")
            ->groupBy('p')
            ->orderBy('p')
            ->pluck('p')
            ->map(fn ($p) => ucwords($p))
            ->values()
            ->all();

        return view('admin.drug_guide.index', [
            'products'       => $products,
            'forms'          => $forms,
            'cats'           => $cats,
            'mfgs'           => $mfgs,
            'classGroups'    => $classGroups,
            'packagingTypes' => $packagingTypes,

            // For keeping filter values selected
            'q'             => $q,
            'form'          => $form,
            'cat'           => $cat,
            'mfg'           => $mfg,
            'drugClass'     => $drugClass,
            'packagingType' => $packagingType,
            'sort'          => $sort,
            'dir'           => $dir,
            'per'           => $per,
        ]);
    }

    /** Show a single product with related companies/forms/categories. */
    public function show(DrugProduct $product)
    {
        $product->load([
            'substance:id,name',
            'dosageForm:id,name',
            'category:id,name',
            'manufacturer:id,name,country',
            'importer:id,name,country',
            'distributor:id,name,country',
            'trader:id,name,country',
        ]);

        return view('admin.drug_guide.show', compact('product'));
    }

    /** New product form. */
    public function create()
    {
        $substances = DrugSubstance::orderBy('name')->get(['id', 'name']);
        $forms      = DrugDosageForm::orderBy('name')->get(['id', 'name']);
        $cats       = DrugPharmacologicCategory::orderBy('name')->get(['id', 'name']);
        $companies  = DrugCompany::orderBy('name')->get(['id', 'name', 'country']);

        // Reuse same company list for all roles
        $mfgs         = $companies;
        $importers    = $companies;
        $distributors = $companies;
        $traders      = $companies;

        return view('admin.drug_guide.create', compact(
            'substances',
            'forms',
            'cats',
            'mfgs',
            'importers',
            'distributors',
            'traders'
        ));
    }

    /** Store new product. */
    public function store(Request $request)
    {
        $data    = $this->validatePayload($request);
        $product = DrugProduct::create($data);

        return redirect()
            ->route('admin.drug_guide.show', $product->id)
            ->with('ok', 'Drug product created.');
    }

    /** Edit form. */
    public function edit(DrugProduct $product)
    {
        $substances = DrugSubstance::orderBy('name')->get(['id', 'name']);
        $forms      = DrugDosageForm::orderBy('name')->get(['id', 'name']);
        $cats       = DrugPharmacologicCategory::orderBy('name')->get(['id', 'name']);
        $companies  = DrugCompany::orderBy('name')->get(['id', 'name', 'country']);

        $mfgs         = $companies;
        $importers    = $companies;
        $distributors = $companies;
        $traders      = $companies;

        return view('admin.drug_guide.edit', compact(
            'product',
            'substances',
            'forms',
            'cats',
            'mfgs',
            'importers',
            'distributors',
            'traders'
        ));
    }

    /** Update product. */
    public function update(Request $request, DrugProduct $product)
    {
        $data = $this->validatePayload($request);
        $product->update($data);

        return redirect()
            ->route('admin.drug_guide.edit', $product->id)
            ->with('ok', 'Drug product updated.');
    }

    /** Delete product. */
    public function destroy(DrugProduct $product)
    {
        $name = $product->brand_name ?? 'Drug product';

        try {
            $product->delete();

            return redirect()
                ->route('admin.drug_guide.index')
                ->with('ok', "Deleted {$name}.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.drug_guide.index')
                ->with('error', "Cannot delete {$name}: {$e->getMessage()}");
        }
    }

    /** Shared validation rules for create/update. */
    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'brand_name'                => ['required', 'string', 'max:255'],
            'substance_id'              => ['required', 'integer', Rule::exists('drug_substances', 'id')],
            'pharmacologic_category_id' => ['required', 'integer', Rule::exists('drug_pharmacologic_categories', 'id')],
            'dosage_form_id'            => ['nullable', 'integer', Rule::exists('drug_dosage_forms', 'id')],
            'dosage_strength'           => ['nullable', 'string', 'max:255'],
            'packaging'                 => ['nullable', 'string'],
            'classification'            => ['nullable', 'string', 'max:255'],
            'country_of_origin'         => ['nullable', 'string', 'max:255'],
            'application_type'          => ['nullable', 'string', 'max:255'],

            'manufacturer_id'           => ['nullable', 'integer', Rule::exists('drug_companies', 'id')],
            'importer_id'               => ['nullable', 'integer', Rule::exists('drug_companies', 'id')],
            'distributor_id'            => ['nullable', 'integer', Rule::exists('drug_companies', 'id')],
            'trader_id'                 => ['nullable', 'integer', Rule::exists('drug_companies', 'id')],

            'registration_number'       => ['nullable', 'string', 'max:255'],
            'issued_at'                 => ['nullable', 'date'],
            'expires_at'                => ['nullable', 'date', 'after_or_equal:issued_at'],
        ]);
    }
}
