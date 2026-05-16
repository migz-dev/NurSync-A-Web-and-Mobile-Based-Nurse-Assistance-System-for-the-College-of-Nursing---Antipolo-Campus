<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient; // chartings_patients
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Main patient listing (active + discharged + archived depending on filters).
     */
    public function index(Request $request)
    {
        [$patients, $filters, $wards, $perPageOptions] = $this->buildListing($request);

        // AJAX / JSON response for filters + pagination
        if ($request->wantsJson()) {
            return response()->json([
                'rows'  => view('admin.patients._rows', [
                    'patients' => $patients,
                ])->render(),
                'pager' => view('admin.patients._pager', [
                    'patients' => $patients,
                ])->render(),
            ]);
        }

        // Full page load
        return view('admin.patients.index', [
            'patients'        => $patients,
            'filters'         => $filters,
            'wards'           => $wards,
            'perPageOptions'  => $perPageOptions,
        ]);
    }

    /**
     * Archives view: same UI but pre-filtered to status = archived.
     */
    public function archived(Request $request)
    {
        // Force status filter to 'archived'
        $request->merge(['status' => 'archived']);

        [$patients, $filters, $wards, $perPageOptions] = $this->buildListing($request);

        if ($request->wantsJson()) {
            return response()->json([
                'rows'  => view('admin.patients._rows', [
                    'patients' => $patients,
                ])->render(),
                'pager' => view('admin.patients._pager', [
                    'patients' => $patients,
                ])->render(),
            ]);
        }

        return view('admin.patients.index', [
            'patients'        => $patients,
            'filters'         => $filters,
            'wards'           => $wards,
            'perPageOptions'  => $perPageOptions,
        ]);
    }

    /**
     * Show a single patient record (read-only from the Admin perspective).
     */
    public function show(Patient $patient)
    {
        // You can create a dedicated admin patient view page later.
        // For now, just pass the patient to a placeholder view.
        return view('admin.patients.show', [
            'patient' => $patient,
        ]);
    }

    /**
     * Archive a patient record (no delete).
     */
    public function archive(Request $request, Patient $patient)
    {
        // Optional: you can add policy/authorization checks here if needed.
        $patient->status = 'archived';
        $patient->save();

        return back()->with('success', 'Patient record has been archived.');
    }

    /* ============================================================
     *  Internal helpers
     * ============================================================ */

    /**
     * Build the common listing data (query, filters, ward options, per-page).
     *
     * @return array [patients, filters, wards, perPageOptions]
     */
    protected function buildListing(Request $request): array
    {
        $filters = [
            'q'      => $request->input('q'),
            'ward'   => $request->input('ward'),
            'status' => $request->input('status'),
            'per'    => $request->input('per'),
        ];

        $query = Patient::query()
            ->with(['faculty' => function ($q) {
                $q->select('id', 'full_name', 'email'); // adjust columns if needed
            }]);

        // Search: name / MRN / ward / attending / etc.
        if ($filters['q']) {
            $q = trim($filters['q']);
            $query->where(function ($sub) use ($q) {
                $like = '%' . $q . '%';
                $sub->where('hospital_no', 'like', $like)
                    ->orWhere('mrn', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('first_name', 'like', $like)
                    ->orWhere('middle_name', 'like', $like)
                    ->orWhere('suffix', 'like', $like)
                    ->orWhere('ward', 'like', $like)
                    ->orWhere('attending_physician', 'like', $like);
            });
        }

        // Ward filter
        if ($filters['ward']) {
            $query->where('ward', $filters['ward']);
        }

        // Status filter: active / discharged / archived
        if ($filters['status']) {
            $allowed = ['active', 'discharged', 'archived'];
            if (in_array($filters['status'], $allowed, true)) {
                $query->where('status', $filters['status']);
            }
        }

        // Default sort: newest admission first, then latest created
        $query->orderByDesc('admission_date')
              ->orderByDesc('id');

        // Per-page options
        $perPageOptions = [10, 15, 25, 50];
        $perPage = (int) ($filters['per'] ?: 15);
        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = 15;
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $patients */
        $patients = $query->paginate($perPage)->appends(array_filter($filters));

        // Ward options (for filter dropdown)
        $wards = Patient::query()
            ->select('ward')
            ->whereNotNull('ward')
            ->where('ward', '!=', '')
            ->distinct()
            ->orderBy('ward')
            ->pluck('ward');

        return [$patients, $filters, $wards, $perPageOptions];
    }
}
