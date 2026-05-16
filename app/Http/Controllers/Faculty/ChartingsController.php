<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Patient;               // chartings_patients
use App\Models\NursesNote;            // chartings_nurses_notes
use App\Models\VitalSign;             // chartings_vitals
use App\Models\IntakeOutput;          // chartings_intake_outputs
use App\Models\MarEntry;              // chartings_mar
use App\Models\NursingCarePlan;       // chartings_ncp
use App\Models\TreatmentProcedure;    // chartings_treatment_records
use App\Models\PatientAssessment;     // chartings_patient_assessments
use App\Models\ShiftHandover;         // chartings_shift_handover
use App\Models\KardexEntry;           // chartings_kardex
use App\Models\PatientSummary;        // chartings_patient_summary

// NEW CHARTINGS
use App\Models\ChartingDiagnosticResult;   // chartings_diagnostic_results
use App\Models\ChartingPatientEducation;   // chartings_patient_education
use App\Models\ChartingMedPrep;           // chartings_med_prep
use App\Models\ChartingAllergy;           // chartings_allergies
use App\Models\ChartingPainAssessment;    // chartings_pain_assessment
use App\Models\ChartingSafetyFallRisk;    // chartings_safety_fallrisk
use App\Models\ChartingNeuroObservation;  // chartings_neuro_observation

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ChartingsController extends Controller
{
    /** ---------- Utilities ---------- */

    /** Guard: CI must own the patient */
    protected function ensureOwner(Patient $patient): void
    {
        $authId = (int) (Auth::guard('faculty')->id() ?? 0);
        if ($patient->faculty_id && $patient->faculty_id !== $authId) {
            abort(403);
        }
    }

    /**
     * Build a baseline query (owned + for patient) and order by the given column if it exists,
     * otherwise fall back to created_at.
     */
    protected function baseOwnedPatientQuery($modelClass, int $facultyId, int $patientId, string $orderCol)
    {
        $q = $modelClass::query()
            ->where('faculty_id', $facultyId)
            ->where('patient_id', $patientId);

        // Order by domain-specific column if present, else created_at
        $instance = new $modelClass;
        $col      = $orderCol;
        $hasCol   = in_array($col, $instance->getFillable(), true)
            || \Schema::hasColumn($instance->getTable(), $col);

        $q->orderByDesc($hasCol ? $col : 'created_at');

        return $q;
    }

    /**
     * BMI classification helper (panel request: indicate if normal / obese, etc.)
     *
     * Uses standard adult BMI cutoffs:
     * <18.5 Underweight
     * 18.5–24.9 Healthy weight
     * 25.0–29.9 Overweight
     * ≥30.0 Obesity
     */
    protected function classifyBmi(?float $bmi): ?string
    {
        if ($bmi === null || $bmi <= 0) {
            return null;
        }

        if ($bmi < 18.5) {
            return 'Underweight';
        } elseif ($bmi < 25) {
            return 'Healthy weight';
        } elseif ($bmi < 30) {
            return 'Overweight';
        }

        return 'Obesity';
    }

    /** ---------- Main pages ---------- */

    /** GET /faculty/chartings — list of active/discharged patients (owned by CI) */
    public function index(Request $request)
    {
        $uid = Auth::guard('faculty')->id();

        $patients = Patient::query()
            ->when($uid, fn ($q) => $q->where('faculty_id', $uid))
            ->where(function ($q) {
                $q->whereNull('archived_at')
                  ->orWhere('status', '!=', 'archived');
            })
            ->orderByRaw("CASE status WHEN 'active' THEN 0 WHEN 'discharged' THEN 1 ELSE 2 END")
            ->orderByDesc('admission_date')
            ->orderByDesc('created_at')
            ->get();

        return view('faculty.chartings', compact('patients'));
    }

    /** GET /faculty/chartings/archives — archived patients */
    public function archivesIndex()
    {
        $uid = Auth::guard('faculty')->id();

        $patients = Patient::query()
            ->when($uid, fn ($q) => $q->where('faculty_id', $uid))
            ->where('status', 'archived')
            ->whereNull('deleted_at')
            ->orderByDesc('archived_at')
            ->orderByDesc('updated_at')
            ->get();

        return view('faculty.chartings-archives', compact('patients'));
    }

    /** GET /faculty/chartings/patient/{patient} — hub (cards) */
    public function showPatient(Patient $patient)
    {
        $this->ensureOwner($patient);
        $authId = (int) (Auth::guard('faculty')->id() ?? 0);

        // Existing 10 core chartings
        $notes  = $this->baseOwnedPatientQuery(NursesNote::class,   $authId, $patient->id, 'logged_at')->limit(20)->get();
        $vitals = $this->baseOwnedPatientQuery(VitalSign::class,    $authId, $patient->id, 'taken_at')->limit(20)->get();
        $ios    = $this->baseOwnedPatientQuery(IntakeOutput::class, $authId, $patient->id, 'logged_at')->limit(20)->get();
        $mars   = $this->baseOwnedPatientQuery(MarEntry::class,     $authId, $patient->id, 'administered_at')->limit(20)->get();
        $ncps   = $this->baseOwnedPatientQuery(NursingCarePlan::class,$authId,$patient->id,'started_at')->limit(20)->get();

        $treatments = $this->baseOwnedPatientQuery(TreatmentProcedure::class, $authId, $patient->id, 'performed_at')->limit(20)->get();
        $assessments= $this->baseOwnedPatientQuery(PatientAssessment::class,  $authId, $patient->id, 'assessed_at')->limit(20)->get();
        $shifts     = $this->baseOwnedPatientQuery(ShiftHandover::class,      $authId, $patient->id, 'handed_over_at')->limit(20)->get();
        $kardexes   = $this->baseOwnedPatientQuery(KardexEntry::class,        $authId, $patient->id, 'updated_for')->limit(20)->get();
        $summaries  = $this->baseOwnedPatientQuery(PatientSummary::class,     $authId, $patient->id, 'logged_at')->limit(20)->get();

        // NEW 7 chartings (for counts / future use)
        $diagnostics = $this->baseOwnedPatientQuery(ChartingDiagnosticResult::class, $authId, $patient->id, 'result_date')->limit(20)->get();
        $educations  = $this->baseOwnedPatientQuery(ChartingPatientEducation::class, $authId, $patient->id, 'created_at')->limit(20)->get();
        $medPreps    = $this->baseOwnedPatientQuery(ChartingMedPrep::class,          $authId, $patient->id, 'time_prepared')->limit(20)->get();
        $allergies   = $this->baseOwnedPatientQuery(ChartingAllergy::class,          $authId, $patient->id, 'date_observed')->limit(20)->get();
        $pains       = $this->baseOwnedPatientQuery(ChartingPainAssessment::class,   $authId, $patient->id, 'assessment_time')->limit(20)->get();
        $safety      = $this->baseOwnedPatientQuery(ChartingSafetyFallRisk::class,   $authId, $patient->id, 'assessment_time')->limit(20)->get();
        $neuro       = $this->baseOwnedPatientQuery(ChartingNeuroObservation::class, $authId, $patient->id, 'assessment_time')->limit(20)->get();

        $counts = [
            'notes'        => $notes->count(),
            'vitals'       => $vitals->count(),
            'io'           => $ios->count(),
            'mar'          => $mars->count(),
            'ncp'          => $ncps->count(),
            'treatment'    => $treatments->count(),
            'assessment'   => $assessments->count(),
            'shift'        => $shifts->count(),
            'kardex'       => $kardexes->count(),
            'summary'      => $summaries->count(),

            'diagnostics'  => $diagnostics->count(),
            'education'    => $educations->count(),
            'med_prep'     => $medPreps->count(),
            'allergies'    => $allergies->count(),
            'pain'         => $pains->count(),
            'safety'       => $safety->count(),
            'neuro'        => $neuro->count(),
        ];

        return view('faculty.chartings-patient', [
            'patient' => $patient,
            'charts'  => [
                'nurses_notes'   => $notes,
                'vitals'         => $vitals,
                'intake_output'  => $ios,
                'mar'            => $mars,
                'ncp'            => $ncps,
                'treatments'     => $treatments,
                'assessments'    => $assessments,
                'shift_handover' => $shifts,
                'kardex'         => $kardexes,
                'summaries'      => $summaries,

                'diagnostics'    => $diagnostics,
                'education'      => $educations,
                'med_preps'      => $medPreps,
                'allergies'      => $allergies,
                'pain'           => $pains,
                'safety'         => $safety,
                'neuro'          => $neuro,
            ],
            'counts'  => $counts,
        ]);
    }

    /** ---------- Per-charting “View” pages (accordion) ---------- */

    public function notesIndex(Patient $patient)
    {
        $this->ensureOwner($patient);
        $uid = (int) Auth::guard('faculty')->id();
        $records = $this->baseOwnedPatientQuery(NursesNote::class, $uid, $patient->id, 'logged_at')->get();
        return view('faculty.chartings.notes-index', compact('patient','records'));
    }

    public function vitalsIndex(Patient $patient)
    {
        $this->ensureOwner($patient);
        $uid = (int) Auth::guard('faculty')->id();
        $records = $this->baseOwnedPatientQuery(VitalSign::class, $uid, $patient->id, 'taken_at')->get();
        return view('faculty.chartings.vitals-index', compact('patient','records'));
    }

    public function ioIndex(Patient $patient)
    {
        $this->ensureOwner($patient);
        $uid = (int) Auth::guard('faculty')->id();
        $records = $this->baseOwnedPatientQuery(IntakeOutput::class, $uid, $patient->id, 'logged_at')->get();
        return view('faculty.chartings.io-index', compact('patient','records'));
    }

    public function marIndex(Patient $patient)
    {
        $this->ensureOwner($patient);
        $uid = (int) Auth::guard('faculty')->id();
        $records = $this->baseOwnedPatientQuery(MarEntry::class, $uid, $patient->id, 'administered_at')->get();
        return view('faculty.chartings.mar-index', compact('patient','records'));
    }

    public function ncpIndex(Patient $patient)
    {
        $this->ensureOwner($patient);
        $uid = (int) Auth::guard('faculty')->id();
        $records = $this->baseOwnedPatientQuery(NursingCarePlan::class, $uid, $patient->id, 'started_at')->get();
        return view('faculty.chartings.ncp-index', compact('patient','records'));
    }

    public function treatmentIndex(Patient $patient)
    {
        $this->ensureOwner($patient);
        $uid = (int) Auth::guard('faculty')->id();
        $records = $this->baseOwnedPatientQuery(TreatmentProcedure::class, $uid, $patient->id, 'performed_at')->get();
        return view('faculty.chartings.treatment-index', compact('patient','records'));
    }

    public function assessmentIndex(Patient $patient)
    {
        $this->ensureOwner($patient);
        $uid = (int) Auth::guard('faculty')->id();
        $records = $this->baseOwnedPatientQuery(PatientAssessment::class, $uid, $patient->id, 'assessed_at')->get();
        return view('faculty.chartings.assessment-index', compact('patient','records'));
    }

    public function shiftIndex(Patient $patient)
    {
        $this->ensureOwner($patient);
        $uid = (int) Auth::guard('faculty')->id();
        $records = $this->baseOwnedPatientQuery(ShiftHandover::class, $uid, $patient->id, 'handed_over_at')->get();
        return view('faculty.chartings.shift-index', compact('patient','records'));
    }

    public function kardexIndex(Patient $patient)
    {
        $this->ensureOwner($patient);
        $uid = (int) Auth::guard('faculty')->id();
        $records = $this->baseOwnedPatientQuery(KardexEntry::class, $uid, $patient->id, 'updated_for')->get();
        return view('faculty.chartings.kardex-index', compact('patient','records'));
    }

    public function summaryIndex(Patient $patient)
    {
        $this->ensureOwner($patient);
        $uid = (int) Auth::guard('faculty')->id();
        $records = $this->baseOwnedPatientQuery(PatientSummary::class, $uid, $patient->id, 'logged_at')->get();
        return view('faculty.chartings.summary-index', compact('patient','records'));
    }

    // NEW per-charting index pages (for future views)

    public function diagnosticIndex(Patient $patient)
    {
        $this->ensureOwner($patient);
        $uid     = (int) Auth::guard('faculty')->id();
        $records = $this->baseOwnedPatientQuery(ChartingDiagnosticResult::class, $uid, $patient->id, 'result_date')->get();
        return view('faculty.chartings.diagnostic-index', compact('patient', 'records'));
    }

    public function educationIndex(Patient $patient)
    {
        $this->ensureOwner($patient);
        $uid     = (int) Auth::guard('faculty')->id();
        $records = $this->baseOwnedPatientQuery(ChartingPatientEducation::class, $uid, $patient->id, 'created_at')->get();
        return view('faculty.chartings.education-index', compact('patient', 'records'));
    }

    public function medPrepIndex(Patient $patient)
    {
        $this->ensureOwner($patient);
        $uid     = (int) Auth::guard('faculty')->id();
        $records = $this->baseOwnedPatientQuery(ChartingMedPrep::class, $uid, $patient->id, 'time_prepared')->get();
        return view('faculty.chartings.medprep-index', compact('patient', 'records'));
    }

    public function allergiesIndex(Patient $patient)
    {
        $this->ensureOwner($patient);
        $uid     = (int) Auth::guard('faculty')->id();
        $records = $this->baseOwnedPatientQuery(ChartingAllergy::class, $uid, $patient->id, 'date_observed')->get();
        return view('faculty.chartings.allergies-index', compact('patient', 'records'));
    }

    public function painIndex(Patient $patient)
    {
        $this->ensureOwner($patient);
        $uid     = (int) Auth::guard('faculty')->id();
        $records = $this->baseOwnedPatientQuery(ChartingPainAssessment::class, $uid, $patient->id, 'assessment_time')->get();
        return view('faculty.chartings.pain-index', compact('patient', 'records'));
    }

    public function safetyIndex(Patient $patient)
    {
        $this->ensureOwner($patient);
        $uid     = (int) Auth::guard('faculty')->id();
        $records = $this->baseOwnedPatientQuery(ChartingSafetyFallRisk::class, $uid, $patient->id, 'assessment_time')->get();
        return view('faculty.chartings.safety-index', compact('patient', 'records'));
    }

    public function neuroIndex(Patient $patient)
    {
        $this->ensureOwner($patient);
        $uid     = (int) Auth::guard('faculty')->id();
        $records = $this->baseOwnedPatientQuery(ChartingNeuroObservation::class, $uid, $patient->id, 'assessment_time')->get();
        return view('faculty.chartings.neuro-index', compact('patient', 'records'));
    }

    /** ---------- Patients: create/update/archive ---------- */

    public function storePatient(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'hospital_no'        => ['nullable', 'string', 'max:64'],
            'last_name'          => ['required', 'string', 'max:100'],
            'first_name'         => ['required', 'string', 'max:100'],
            'middle_name'        => ['nullable', 'string', 'max:100'],
            'suffix'             => ['nullable', 'string', 'max:20'],
            'sex'                => ['nullable', 'in:M,F,I,U'],
            'dob'                => ['nullable', 'date'],
            'age'                => ['nullable', 'integer', 'min:0', 'max:130'],
            'contact_no'         => ['nullable', 'string', 'max:20'],
            'address'            => ['nullable', 'string'],
            'attending_physician'=> ['nullable', 'string', 'max:191'],
            'admitting_diagnosis'=> ['nullable', 'string', 'max:255'],
            'ward'               => ['nullable', 'string', 'max:50'],
            'bed_no'             => ['nullable', 'string', 'max:50'],
            'admission_date'     => ['nullable', 'date'],
            'status'             => ['nullable', 'in:active,discharged,archived'],
            'notes'              => ['nullable', 'string'],
        ]);

        $data['faculty_id'] = Auth::guard('faculty')->id();
        $data['status']     = $data['status'] ?? 'active';

        Patient::create($data);

        return redirect()->route('faculty.chartings.index')
            ->with('success', 'Patient created successfully.');
    }

    public function updatePatient(Request $request, Patient $patient): JsonResponse|RedirectResponse
    {
        $this->ensureOwner($patient);

        $data = $request->validate([
            'hospital_no'        => ['nullable', 'string', 'max:64'],
            'last_name'          => ['required', 'string', 'max:100'],
            'first_name'         => ['required', 'string', 'max:100'],
            'middle_name'        => ['nullable', 'string', 'max:100'],
            'suffix'             => ['nullable', 'string', 'max:20'],
            'sex'                => ['nullable', 'in:M,F,I,U'],
            'dob'                => ['nullable', 'date'],
            'age'                => ['nullable', 'integer', 'min:0', 'max:130'],
            'contact_no'         => ['nullable', 'string', 'max:20'],
            'address'            => ['nullable', 'string'],
            'attending_physician'=> ['nullable', 'string', 'max:191'],
            'admitting_diagnosis'=> ['nullable', 'string', 'max:255'],
            'ward'               => ['nullable', 'string', 'max:50'],
            'bed_no'             => ['nullable', 'string', 'max:50'],
            'admission_date'     => ['nullable', 'date'],
            'status'             => ['nullable', 'in:active,discharged,archived'],
            'notes'              => ['nullable', 'string'],
        ]);

        if (($data['status'] ?? null) === 'archived' && is_null($patient->archived_at)) {
            $patient->archived_at = now();
        }
        if (($data['status'] ?? null) !== 'archived') {
            $patient->archived_at = null;
        }

        $patient->fill($data)->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'id'      => $patient->id,
                'status'  => $patient->status,
                'message' => 'Patient updated.',
            ]);
        }

        return redirect()->route('faculty.chartings.index')->with('success', 'Patient updated.');
    }

    public function archivePatient(Request $request, Patient $patient): JsonResponse|RedirectResponse
    {
        $this->ensureOwner($patient);

        if ($patient->status === 'archived' || $patient->archived_at) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'id'      => $patient->id,
                    'status'  => 'archived',
                    'message' => 'Already archived.',
                ]);
            }
            return redirect()->route('faculty.chartings.index')->with('success', 'Patient already archived.');
        }

        $patient->status      = 'archived';
        $patient->archived_at = now();
        $patient->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'id'      => $patient->id,
                'status'  => 'archived',
                'message' => 'Patient archived.',
            ]);
        }

        return redirect()->route('faculty.chartings.index')->with('success', 'Patient archived.');
    }

    public function restorePatient(Request $request, Patient $patient): JsonResponse|RedirectResponse
    {
        $this->ensureOwner($patient);

        $patient->status      = 'active';
        $patient->archived_at = null;
        $patient->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'id'      => $patient->id,
                'status'  => 'active',
                'message' => 'Patient restored.',
            ]);
        }

        return redirect()->route('faculty.chartings.archives.index')->with('success', 'Patient restored.');
    }

    public function destroyPatient(Request $request, Patient $patient): JsonResponse|RedirectResponse
    {
        $this->ensureOwner($patient);

        if ($patient->status !== 'archived') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Patient must be archived first.'], 422);
            }
            return redirect()->back()->with('error', 'Patient must be archived first.');
        }

        $patient->forceDelete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'id'      => $patient->id,
                'message' => 'Patient permanently deleted.',
            ]);
        }

        return redirect()->route('faculty.chartings.archives.index')->with('success', 'Patient permanently deleted.');
    }

    /** ---------- First 5: Store ---------- */

    public function storeNotes(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureOwner($patient);

        $data = $request->validate([
            'logged_at'  => ['required','date'],
            'note_type'  => ['required','string','max:16'], // DAR|SOAP|PIE|FOCUS|NARRATIVE
            'narrative'  => ['nullable','string'],
            'subjective' => ['nullable','string'],
            'objective'  => ['nullable','string'],
            'assessment' => ['nullable','string'],
            'plan'       => ['nullable','string'],
            'status'     => ['nullable','in:draft,final'],
        ]);

        $data['patient_id'] = $patient->id;
        $data['faculty_id'] = Auth::guard('faculty')->id();
        $data['status']     = $data['status'] ?? 'final';

        NursesNote::create($data);

        return redirect()->route('faculty.chartings.patient', $patient)->with('success', 'Nurse’s note added.');
    }

    public function storeVitals(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureOwner($patient);

        $data = $request->validate([
            'taken_at'        => ['required','date'],
            'temp_c'          => ['nullable','numeric','between:30,45'],
            'heart_rate_bpm'  => ['nullable','integer','between:0,250'],
            'resp_rate_cpm'   => ['nullable','integer','between:0,80'],
            'bp_systolic'     => ['nullable','integer','between:40,300'],
            'bp_diastolic'    => ['nullable','integer','between:20,200'],
            'spo2_pct'        => ['nullable','integer','between:0,100'],
            'pain_score'      => ['nullable','integer','between:0,10'],
            'remarks'         => ['nullable','string'],

            // Anthropometrics + derived indices (panel request)
            'height_cm'       => ['nullable','numeric','min:0','max:300'],
            'weight_kg'       => ['nullable','numeric','min:0','max:400'],
            'bmi'             => ['nullable','numeric','min:0','max:100'],
            'bsa_m2'          => ['nullable','numeric','min:0','max:5'],
            'bmi_category'    => ['nullable','string','max:50'],
        ]);

        // Build BP string if both components present
        if (!empty($data['bp_systolic']) && !empty($data['bp_diastolic'])) {
            $data['bp'] = $data['bp_systolic'].'/'.$data['bp_diastolic'];
        }

        // Server-side BMI/BSA recomputation for accuracy
        $weight = isset($data['weight_kg']) ? (float) $data['weight_kg'] : null;
        $heightCm = isset($data['height_cm']) ? (float) $data['height_cm'] : null;

        if ($weight && $heightCm) {
            $hMeters = $heightCm / 100.0;

            if ($hMeters > 0) {
                // Metric BMI formula: kg / (m²)
                $bmi = $weight / ($hMeters * $hMeters);

                // Mosteller BSA formula: √((cm × kg) / 3600)
                $bsa = sqrt(($weight * $heightCm) / 3600.0);

                $data['bmi']    = round($bmi, 2);
                $data['bsa_m2'] = round($bsa, 2);
            }
        }

        // Ensure BMI category derived from BMI value (even if front-end didn't send it)
        if (!empty($data['bmi'])) {
            $category = $this->classifyBmi((float) $data['bmi']);
            if ($category !== null) {
                $data['bmi_category'] = $category;
            }
        }

        $data['patient_id'] = $patient->id;
        $data['faculty_id'] = Auth::guard('faculty')->id();

        VitalSign::create($data);

        return redirect()->route('faculty.chartings.patient', $patient)->with('success', 'Vital signs recorded.');
    }

    public function storeIntakeOutput(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureOwner($patient);

        $data = $request->validate([
            'logged_at'          => ['required','date'],
            'remarks'            => ['nullable','string'],

            'intake_ml'          => ['nullable','integer','min:0'],
            'output_ml'          => ['nullable','integer','min:0'],

            'intake_oral_ml'     => ['nullable','integer','min:0'],
            'intake_iv_ml'       => ['nullable','integer','min:0'],
            'intake_ng_ml'       => ['nullable','integer','min:0'],

            'output_urine_ml'    => ['nullable','integer','min:0'],
            'output_stool_ml'    => ['nullable','integer','min:0'],
            'output_emesis_ml'   => ['nullable','integer','min:0'],
            'output_drain_ml'    => ['nullable','integer','min:0'],
        ]);

        $intakeParts = [
            (int)($data['intake_oral_ml'] ?? 0),
            (int)($data['intake_iv_ml']   ?? 0),
            (int)($data['intake_ng_ml']   ?? 0),
        ];
        $outputParts = [
            (int)($data['output_urine_ml']  ?? 0),
            (int)($data['output_stool_ml']  ?? 0),
            (int)($data['output_emesis_ml'] ?? 0),
            (int)($data['output_drain_ml']  ?? 0),
        ];

        $data['intake_ml'] = array_key_exists('intake_ml', $data)
            ? (int)$data['intake_ml']
            : array_sum($intakeParts);

        $data['output_ml'] = array_key_exists('output_ml', $data)
            ? (int)$data['output_ml']
            : array_sum($outputParts);

        $data['balance_ml'] = $data['intake_ml'] - $data['output_ml'];

        $data['patient_id'] = $patient->id;
        $data['faculty_id'] = Auth::guard('faculty')->id();

        IntakeOutput::create($data);

        return redirect()->route('faculty.chartings.patient', $patient)->with('success', 'I&O entry added.');
    }

    public function storeMar(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureOwner($patient);

        $data = $request->validate([
            'scheduled_time'  => ['nullable','date'],
            'administered_at' => ['nullable','date'],
            'drug_name'       => ['required','string','max:191'],
            'dose'            => ['nullable','string','max:100'],
            'route'           => ['nullable','string','max:50'],
            'frequency'       => ['nullable','string','max:100'],
            'status'          => ['nullable','string','max:20'],
            'given_by'        => ['nullable','string','max:191'],
            'indication'      => ['nullable','string'],
            'remarks'         => ['nullable','string'],
        ]);

        $map = [
            'given'      => 'Given',
            'held'       => 'Held',
            'missed'     => 'Missed',
            'refused'    => 'Refused',
            'scheduled'  => 'Scheduled',
            'npo'        => 'Held',
        ];
        if (!empty($data['status'])) {
            $key = strtolower(trim($data['status']));
            $data['status'] = $map[$key] ?? $data['status'];
        }

        $data['patient_id'] = $patient->id;
        $data['faculty_id'] = Auth::guard('faculty')->id();

        MarEntry::create($data);

        return redirect()->route('faculty.chartings.patient', $patient)->with('success', 'MAR entry added.');
    }

    public function storeNcp(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureOwner($patient);

        $data = $request->validate([
            'started_at'          => ['required','date'],
            'dx_primary'          => ['required','string','max:255'],
            'dx_related_to'       => ['nullable','string','max:255'],
            'dx_as_evidenced_by'  => ['nullable','string','max:500'],
            'goals'               => ['nullable','string'],
            'interventions'       => ['nullable','string'],
            'outcomes_evaluation' => ['nullable','string'],
            'status'              => ['nullable','string','max:20'],
            'reviewed_at'         => ['nullable','date'],
        ]);

        $status         = strtolower(trim($data['status'] ?? 'ongoing'));
        $map            = ['met' => 'met', 'ongoing' => 'ongoing', 'revised' => 'revised', 'discontinued' => 'discontinued'];
        $data['status'] = $map[$status] ?? 'ongoing';

        $data['patient_id'] = $patient->id;
        $data['faculty_id'] = Auth::guard('faculty')->id();

        NursingCarePlan::create($data);

        return redirect()->route('faculty.chartings.patient', $patient)->with('success', 'NCP created.');
    }

    /** ---------- Next 5: Store ---------- */

    public function storeTreatment(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureOwner($patient);

        $data = $request->validate([
            'performed_at'   => ['required','date'],
            'procedure_name' => ['required','string','max:191'],
            'indication'     => ['nullable','string','max:255'],
            'details'        => ['nullable','string'],
            'outcome'        => ['nullable','string','max:255'],
            'performed_by'   => ['nullable','string','max:191'],
            'observed_by'    => ['nullable','string','max:191'],
            'complications'  => ['nullable','string','max:255'],
            'remarks'        => ['nullable','string'],
        ]);

        $data['patient_id'] = $patient->id;
        $data['faculty_id'] = Auth::guard('faculty')->id();

        TreatmentProcedure::create($data);

        return redirect()->route('faculty.chartings.patient', $patient)->with('success', 'Treatment/procedure recorded.');
    }

    public function storeAssessment(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureOwner($patient);

        $data = $request->validate([
            'assessed_at'      => ['required','date'],
            'assessment_type'  => ['nullable','string','max:32'],
            'chief_complaint'  => ['nullable','string','max:255'],
            'subjective'       => ['nullable','string'],
            'objective'        => ['nullable','string'],
            'assessment'       => ['nullable','string'],
            'plan'             => ['nullable','string'],
        ]);

        $data['patient_id'] = $patient->id;
        $data['faculty_id'] = Auth::guard('faculty')->id();

        PatientAssessment::create($data);

        return redirect()->route('faculty.chartings.patient', $patient)->with('success', 'Assessment saved.');
    }

    public function storeShift(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureOwner($patient);

        $data = $request->validate([
            'handed_over_at' => ['required','date'],
            'shift'          => ['nullable','string','max:8'],   // AM|PM|NOC
            'from_nurse'     => ['nullable','string','max:191'],
            'to_nurse'       => ['nullable','string','max:191'],
            'summary'        => ['nullable','string'],
            'pending_orders' => ['nullable','string'],
        ]);

        $data['patient_id'] = $patient->id;
        $data['faculty_id'] = Auth::guard('faculty')->id();

        ShiftHandover::create($data);

        return redirect()->route('faculty.chartings.patient', $patient)->with('success', 'Shift handover logged.');
    }

    public function storeKardex(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureOwner($patient);

        $data = $request->validate([
            'updated_for'    => ['required','date'],
            'diagnosis'      => ['nullable','string','max:255'],
            'diet'           => ['nullable','string','max:191'],
            'activity'       => ['nullable','string','max:191'],
            'medications'    => ['nullable','string'],
            'nursing_orders' => ['nullable','string'],
        ]);

        $data['patient_id'] = $patient->id;
        $data['faculty_id'] = Auth::guard('faculty')->id();

        KardexEntry::create($data);

        return redirect()->route('faculty.chartings.patient', $patient)->with('success', 'Kardex updated.');
    }

    public function storeSummary(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureOwner($patient);

        $data = $request->validate([
            'logged_at'      => ['required','date'],
            'author'         => ['nullable','string','max:191'],
            'progress_notes' => ['nullable','string'],
        ]);

        $data['patient_id'] = $patient->id;
        $data['faculty_id'] = Auth::guard('faculty')->id();

        PatientSummary::create($data);

        return redirect()->route('faculty.chartings.patient', $patient)->with('success', 'Daily progress saved.');
    }

    /** ---------- NEW: Store methods for the 7 additional chartings ---------- */

    public function storeDiagnostic(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureOwner($patient);

        $data = $request->validate([
            'result_type'          => ['required','string','max:100'],
            'result_title'         => ['required','string','max:255'],
            'result_date'          => ['required','date'],
            'significant_findings' => ['nullable','string'],
            'critical_values'      => ['nullable','string'],
            'interpretation_notes' => ['nullable','string'],
            'actions_taken'        => ['nullable','string'],
            'attachment_path'      => ['nullable','string','max:255'],
        ]);

        $data['patient_id'] = $patient->id;
        $data['faculty_id'] = Auth::guard('faculty')->id();

        ChartingDiagnosticResult::create($data);

        return redirect()->route('faculty.chartings.patient', $patient)->with('success', 'Diagnostic result logged.');
    }

    public function storeEducation(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureOwner($patient);

        $data = $request->validate([
            'topic'                 => ['required','string','max:255'],
            'method_used'           => ['nullable','string','max:255'],
            'materials_used'        => ['nullable','string','max:255'],
            'session_notes'         => ['nullable','string'],
            'patient_understanding' => ['nullable','string','max:255'],
            'follow_up_required'    => ['nullable','boolean'],
            'follow_up_notes'       => ['nullable','string'],
        ]);

        $data['follow_up_required'] = (bool)($data['follow_up_required'] ?? false);
        $data['patient_id']         = $patient->id;
        $data['faculty_id']         = Auth::guard('faculty')->id();

        ChartingPatientEducation::create($data);

        return redirect()->route('faculty.chartings.patient', $patient)->with('success', 'Patient education documented.');
    }

    public function storeMedPrep(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureOwner($patient);

        $data = $request->validate([
            'medication_name'         => ['required','string','max:255'],
            'dose'                    => ['nullable','string','max:100'],
            'route'                   => ['nullable','string','max:50'],
            'preparation_steps'       => ['nullable','string'],
            'double_checked_by'       => ['nullable','string','max:255'],
            'safety_checks_completed' => ['nullable','boolean'],
            'time_prepared'           => ['required','date'],
            'remarks'                 => ['nullable','string'],
        ]);

        $data['safety_checks_completed'] = (bool)($data['safety_checks_completed'] ?? false);
        $data['patient_id']              = $patient->id;
        $data['faculty_id']              = Auth::guard('faculty')->id();

        ChartingMedPrep::create($data);

        return redirect()->route('faculty.chartings.patient', $patient)->with('success', 'Medication preparation recorded.');
    }

    public function storeAllergy(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureOwner($patient);

        $data = $request->validate([
            'allergen'      => ['required','string','max:255'],
            'reaction'      => ['required','string','max:255'],
            'severity'      => ['nullable','string','max:50'],
            'date_observed' => ['nullable','date'],
            'notes'         => ['nullable','string'],
            'action_taken'  => ['nullable','string'],
        ]);

        $data['patient_id'] = $patient->id;
        $data['faculty_id'] = Auth::guard('faculty')->id();

        ChartingAllergy::create($data);

        return redirect()->route('faculty.chartings.patient', $patient)->with('success', 'Allergy/reaction recorded.');
    }

    public function storePain(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureOwner($patient);

        // Match modal-pain-create.blade.php
        $data = $request->validate([
            'assessed_at'          => ['required','date'],
            'pain_score'           => ['required','integer','between:0,10'],
            'scale_used'           => ['nullable','string','max:191'],
            'location'             => ['nullable','string','max:255'],
            'characteristics'      => ['nullable','string'],
            'aggravating_factors'  => ['nullable','string'],
            'relieving_factors'    => ['nullable','string'],
            'interventions'        => ['nullable','string'],
            'response'             => ['nullable','string'],
        ]);

        $payload = [
            'patient_id'              => $patient->id,
            'faculty_id'              => Auth::guard('faculty')->id(),
            'assessment_time'         => $data['assessed_at'],              // map datetime-local
            'pain_score'              => $data['pain_score'],
            'location'                => $data['location']            ?? null,
            'characteristics'         => $data['characteristics']     ?? null,
            'aggravating_factors'     => $data['aggravating_factors'] ?? null,
            'relieving_factors'       => $data['relieving_factors']   ?? null,
            'interventions'           => $data['interventions']       ?? null,
            'response_to_intervention'=> $data['response']            ?? null,
        ];

        // Only attach scale_used if the column exists to avoid SQL errors
        $instance = new ChartingPainAssessment;
        if (!empty($data['scale_used']) && \Schema::hasColumn($instance->getTable(), 'scale_used')) {
            $payload['scale_used'] = $data['scale_used'];
        }

        ChartingPainAssessment::create($payload);

        return redirect()->route('faculty.chartings.patient', $patient)->with('success', 'Pain assessment saved.');
    }

    public function storeSafety(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureOwner($patient);

        // Match modal-safety-create.blade.php
        $data = $request->validate([
            'checked_at'             => ['required','date'],
            'tool_used'              => ['nullable','string','max:255'],
            'risk_level'             => ['nullable','string','max:50'],

            'bed_in_low_position'    => ['nullable','boolean'],
            'side_rails_up'          => ['nullable','boolean'],
            'call_bell_within_reach' => ['nullable','boolean'],
            'non_slip_footwear'      => ['nullable','boolean'],
            'environment_safe'       => ['nullable','boolean'],
            'restraints_in_place'    => ['nullable','boolean'],

            'interventions'          => ['nullable','string'],
            'notes'                  => ['nullable','string'],
        ]);

        // Build a readable summary for environment_check
        $items = [];

        if (!empty($data['tool_used'])) {
            $items[] = 'Tool/scale: '.$data['tool_used'];
        }
        if (!empty($data['risk_level'])) {
            $items[] = 'Risk level: '.$data['risk_level'];
        }
        if (!empty($data['bed_in_low_position'])) {
            $items[] = 'Bed in lowest position';
        }
        if (!empty($data['side_rails_up'])) {
            $items[] = 'Side rails up as ordered';
        }
        if (!empty($data['call_bell_within_reach'])) {
            $items[] = 'Call bell within reach';
        }
        if (!empty($data['non_slip_footwear'])) {
            $items[] = 'Non-slip footwear used';
        }
        if (!empty($data['environment_safe'])) {
            $items[] = 'Environment free of clutter/spills';
        }

        $environmentSummary = $items ? implode('; ', $items) : null;

        $payload = [
            'patient_id'       => $patient->id,
            'faculty_id'       => Auth::guard('faculty')->id(),
            'assessment_time'  => $data['checked_at'],               // map datetime-local
            'fall_risk_score'  => null,                              // optional, not from form
            'environment_check'=> $environmentSummary,
            'restraints_in_use'=> !empty($data['restraints_in_place']),
            'restraint_notes'  => $data['notes']        ?? null,
            'safety_measures'  => $data['interventions']?? null,
        ];

        ChartingSafetyFallRisk::create($payload);

        return redirect()->route('faculty.chartings.patient', $patient)->with('success', 'Safety/fall-risk check saved.');
    }

    public function storeNeuro(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureOwner($patient);

        // Match modal-neuro-create.blade.php
        $data = $request->validate([
            'observed_at'   => ['required','date'],
            'loc'           => ['nullable','string','max:191'],
            'orientation'   => ['nullable','string','max:191'],

            'gcs_eye'       => ['nullable','integer','between:1,4'],
            'gcs_verbal'    => ['nullable','integer','between:1,5'],
            'gcs_motor'     => ['nullable','integer','between:1,6'],
            'gcs_total'     => ['nullable','integer','between:3,15'],

            'pupil_left'    => ['nullable','string','max:191'],
            'pupil_right'   => ['nullable','string','max:191'],

            'motor_function'=> ['nullable','string'],
            'sensation'     => ['nullable','string'],
            'notes'         => ['nullable','string'],
        ]);

        // Combine LOC + orientation into a single orientation_status field
        $orientationPieces = array_filter([
            $data['loc']         ?? null,
            $data['orientation'] ?? null,
        ]);
        $orientationStatus = $orientationPieces ? implode(' | ', $orientationPieces) : null;

        $payload = [
            'patient_id'            => $patient->id,
            'faculty_id'            => Auth::guard('faculty')->id(),
            'assessment_time'       => $data['observed_at'],                // map datetime-local

            'gcs_eye'               => $data['gcs_eye']    ?? null,
            'gcs_verbal'            => $data['gcs_verbal'] ?? null,
            'gcs_motor'             => $data['gcs_motor']  ?? null,

            // Store full text in "*_size" fields to keep it simple
            'pupil_left_size'       => $data['pupil_left']  ?? null,
            'pupil_left_reaction'   => null,
            'pupil_right_size'      => $data['pupil_right'] ?? null,
            'pupil_right_reaction'  => null,

            'motor_strength'        => $data['motor_function'] ?? null,
            'sensation'             => $data['sensation']      ?? null,
            'orientation_status'    => $orientationStatus,
            'notes'                 => $data['notes']          ?? null,
        ];

        // If gcs_total column actually exists, either take from form or compute
        $instance = new ChartingNeuroObservation;
        if (\Schema::hasColumn($instance->getTable(), 'gcs_total')) {
            if (!empty($data['gcs_total'])) {
                $payload['gcs_total'] = $data['gcs_total'];
            } elseif (
                isset($data['gcs_eye'], $data['gcs_verbal'], $data['gcs_motor']) &&
                $data['gcs_eye'] !== null && $data['gcs_verbal'] !== null && $data['gcs_motor'] !== null
            ) {
                $payload['gcs_total'] = (int)$data['gcs_eye'] + (int)$data['gcs_verbal'] + (int)$data['gcs_motor'];
            }
        }

        ChartingNeuroObservation::create($payload);

        return redirect()->route('faculty.chartings.patient', $patient)->with('success', 'Neurological observation recorded.');
    }

    /** Placeholder for /faculty/chartings/create */
    public function create()
    {
        abort(404);
    }
}
