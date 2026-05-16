<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Http\Requests\Faculty\StoreIntakeOutputRequest;
use App\Http\Requests\Faculty\UpdateIntakeOutputRequest;
use App\Models\IntakeOutput;
use App\Models\Patient;
use App\Models\Encounter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntakeOutputController extends Controller
{
    public function index(Request $request)
    {
        $ios = IntakeOutput::owned()
            ->with(['patient','encounter'])
            ->when($request->filled('q'), function($q) use ($request) {
                $needle = '%'.$request->q.'%';
                $q->whereHas('patient', fn($qq)=>$qq->where('full_name','like',$needle));
            })
            ->orderByDesc('started_at')
            ->paginate(20)
            ->withQueryString();

        return view('faculty.chartings.io_index', compact('ios'));
    }

    public function create()
    {
        return view('faculty.chartings.io_create');
    }

    public function store(StoreIntakeOutputRequest $request)
    {
        $fid = (int) auth('faculty')->id();

        DB::transaction(function() use ($request, $fid) {
            // quick patient
            $ln = trim((string)$request->input('quick_patient.last_name'));
            $fn = trim((string)$request->input('quick_patient.first_name'));
            $full = trim($ln.', '.$fn, ', ');
            $patient = Patient::create([
                'full_name'   => $full,
                'hospital_no' => $request->input('quick_patient.hospital_no'),
            ]);

            // quick encounter
            $enc = Encounter::create([
                'patient_id' => $patient->id,
                'faculty_id' => $fid,
                'unit'       => $request->input('quick_encounter.unit'),
                'started_at' => $request->input('quick_encounter.started_at'),
                'remarks'    => $request->input('quick_encounter.remarks'),
            ]);

            // I&O record
            IntakeOutput::create(array_merge(
                $request->safe()->only([
                    'started_at','ended_at',
                    'intake_oral_ml','intake_iv_ml','intake_tube_ml',
                    'output_urine_ml','output_stool_ml','output_emesis_ml','output_drain_ml',
                    'remarks',
                ]),
                ['faculty_id'=>$fid, 'patient_id'=>$patient->id, 'encounter_id'=>$enc->id]
            ));
        });

        return redirect()->route('faculty.chartings.io.index')
            ->with('status','Intake & Output saved.');
    }

    public function show(IntakeOutput $io)
    {
        abort_unless($io->faculty_id === auth('faculty')->id(), 403);
        $io->load(['patient','encounter']);
        return view('faculty.chartings.io_show', compact('io'));
    }

    public function edit(IntakeOutput $io)
    {
        abort_unless($io->faculty_id === auth('faculty')->id(), 403);
        $io->load(['patient','encounter']);
        return view('faculty.chartings.io_edit', compact('io'));
    }

    public function update(UpdateIntakeOutputRequest $request, IntakeOutput $io)
    {
        abort_unless($io->faculty_id === auth('faculty')->id(), 403);
        $io->update($request->validated());

        return redirect()->route('faculty.chartings.io.edit', $io)
            ->with('status','Intake & Output updated.');
    }

    public function destroy(IntakeOutput $io)
    {
        abort_unless($io->faculty_id === auth('faculty')->id(), 403);
        $io->delete();

        return redirect()->route('faculty.chartings.io.index')
            ->with('status','Intake & Output deleted.');
    }
}
