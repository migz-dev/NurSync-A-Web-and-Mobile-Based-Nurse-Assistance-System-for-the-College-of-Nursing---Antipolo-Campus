<?php

namespace App\Http\Controllers\Api\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Drug;

class DrugApiController extends Controller
{
    /**
     * GET /api/faculty/drugs
     * Query params:
     *  q=string, class=string, route=string, age=adult|pediatric|geriatric|neonate, core=0|1, page, per_page
     */
    public function index(Request $req)
    {
        $q       = (string) $req->query('q', '');
        $class   = (string) $req->query('class', '');
        $route   = (string) $req->query('route', '');
        $age     = (string) $req->query('age', '');
        $coreRaw = $req->query('core', null);
        $core    = is_null($coreRaw) ? null : (bool) ((int) $coreRaw);
        $perPage = (int) max(1, min(50, (int) $req->query('per_page', 15)));

        $query = Drug::query()
            ->with(['monograph:id,drug_id', 'doses:id,drug_id,population,route'])
            ->search($q)
            ->byClass($class ?: null);

        if (!is_null($core)) {
            $query->where('is_core', $core ? 1 : 0);
        }
        if ($route !== '') {
            $query->whereHas('doses', fn($w) => $w->where('route', $route));
        }
        if ($age !== '') {
            $query->whereHas('doses', fn($w) => $w->where('population', $age));
        }

        $paginator = $query->orderBy('generic_name')->paginate($perPage)->appends($req->query());

        $data = $paginator->getCollection()->map(function (Drug $d) {
            return [
                'id'            => $d->id,
                'generic'       => $d->generic_name,
                'brands'        => $d->brand_names ?? [],
                'class'         => $d->atc_class,
                'pregnancy'     => $d->pregnancy_category,
                'is_core'       => (bool) $d->is_core,
                'last_reviewed' => optional($d->last_reviewed_at)->toISOString(),
                'updated_at'    => optional($d->updated_at)->toISOString(),
            ];
        });

        return response()->json([
            'data'  => $data,
            'meta'  => [
                'page'        => $paginator->currentPage(),
                'per_page'    => $paginator->perPage(),
                'total'       => $paginator->total(),
                'last_page'   => $paginator->lastPage(),
                'has_more'    => $paginator->hasMorePages(),
                'filters'     => [
                    'q'     => $q,
                    'class' => $class,
                    'route' => $route,
                    'age'   => $age,
                    'core'  => $core,
                ],
            ],
            'links' => [
                'self'  => $req->fullUrl(),
                'next'  => $paginator->nextPageUrl(),
                'prev'  => $paginator->previousPageUrl(),
            ],
        ]);
    }

    /**
     * GET /api/faculty/drugs/{id}
     * Full monograph payload
     */
    public function show(int $id)
    {
        $d = Drug::with(['monograph','doses','interactions'])->findOrFail($id);

        $payload = [
            'id'            => $d->id,
            'generic'       => $d->generic_name,
            'brands'        => $d->brand_names ?? [],
            'class'         => $d->atc_class,
            'pregnancy'     => $d->pregnancy_category,
            'lactation'     => $d->lactation_notes,
            'renal_adjust'  => $d->renal_adjust_notes,
            'hepatic_adjust'=> $d->hepatic_adjust_notes,
            'is_core'       => (bool) $d->is_core,
            'last_reviewed' => optional($d->last_reviewed_at)->toISOString(),
            'updated_at'    => optional($d->updated_at)->toISOString(),

            'monograph' => [
                'indications'              => $d->monograph->indications ?? '',
                'mechanism'                => $d->monograph->mechanism ?? '',
                'contraindications'        => $d->monograph->contraindications ?? '',
                'adverse_effects'          => $d->monograph->adverse_effects ?? '',
                'nursing_responsibilities' => $d->monograph->nursing_responsibilities ?? '',
                'patient_teaching'         => $d->monograph->patient_teaching ?? '',
                'monitoring_params'        => $d->monograph->monitoring_params ?? '',
                'references'               => $d->monograph->references ?? [],
            ],

            'doses' => $d->doses->map(fn($x) => [
                'population'    => $x->population,
                'route'         => $x->route,
                'form'          => $x->form,
                'dose_text'     => $x->dose_text,
                'max_dose_text' => $x->max_dose_text,
            ])->values()->all(),

            'interactions' => $d->interactions->map(fn($ix) => [
                'with'       => $ix->interacts_with,
                'severity'   => $ix->severity,
                'mechanism'  => $ix->mechanism,
                'management' => $ix->management,
            ])->values()->all(),
        ];

        return response()->json(['data' => $payload]);
    }
}
