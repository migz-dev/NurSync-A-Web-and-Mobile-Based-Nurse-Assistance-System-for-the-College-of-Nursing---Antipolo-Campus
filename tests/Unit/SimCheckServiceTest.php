<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\SimCheckService;
use App\Models\Sim\{ SimChart, SimChartEntry, SimCheck };

class SimCheckServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function seedMinimalCase(): array
    {
        // Minimal faculty (adjust columns if your schema differs)
        $fid = DB::table('faculty')->insertGetId([
            'name'       => 'Demo CI',
            'email'      => 'ci@example.test',
            'password'   => bcrypt('secret'),
            'approved'   => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $caseId = DB::table('sim_cases')->insertGetId([
            'title'            => 'Post-Op Appendectomy – Adult',
            'summary'          => 'Adult patient recovering.',
            'chief_complaint'  => 'Pain',
            'primary_dx'       => 'S/P Appendectomy',
            'allergies_json'   => json_encode(['Penicillin']),
            'precautions_json' => json_encode(['Fall risk']),
            'created_by'       => $fid,
            'is_active'        => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $aid = DB::table('sim_assignments')->insertGetId([
            'case_id'               => $caseId,
            'faculty_id'            => $fid,
            'title'                 => 'Week 1 Simulation',
            'instructions'          => 'Complete modules.',
            'due_at'                => Carbon::now()->addDays(3),
            'required_modules_json' => json_encode(['vitals','mar','ncp']),
            'visibility'            => 'class',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $chartId = DB::table('sim_charts')->insertGetId([
            'assignment_id' => $aid,
            'student_id'    => 1001, // no FK by default; adjust if you have one
            'status'        => 'in_progress',
            'started_at'    => now(),
            'created_at'    => now(), 'updated_at' => now(),
        ]);

        $chart = SimChart::with(['assignment.simCase', 'entries', 'checks'])->findOrFail($chartId);

        return compact('chart', 'fid', 'caseId', 'aid', 'chartId');
    }

    /** @test */
    public function vitals_out_of_range_creates_warn_checks()
    {
        ['chart' => $chart] = $this->seedMinimalCase();

        SimChartEntry::create([
            'chart_id' => $chart->id,
            'module'   => 'vitals',
            'payload_json' => [
                'bp'     => '150/98',
                'hr'     => 140,
                'rr'     => 8,
                'temp_c' => 39.1,
            ],
        ]);

        $chart->load(['entries','assignment.simCase']);

        $svc = new SimCheckService();
        $hasErrors = $svc->runHard($chart); // vitals should not create ERRORs

        $this->assertFalse($hasErrors, 'Vitals warnings should not produce blocking errors.');
        $this->assertDatabaseHas('sim_checks', [
            'chart_id'  => $chart->id,
            'rule_code' => 'VITALS_RANGE',
            'severity'  => 'warn',
        ]);
    }

    /** @test */
    public function mar_allergy_conflict_is_error_in_hard_mode()
    {
        ['chart' => $chart] = $this->seedMinimalCase();

        // Add MAR entry that conflicts with "Penicillin" allergy
        SimChartEntry::create([
            'chart_id' => $chart->id,
            'module'   => 'mar',
            'payload_json' => [
                'drug' => 'Amoxicillin 500 mg PO', // penicillin class
                'time' => now()->format('Y-m-d\TH:i'),
            ],
        ]);

        $chart->load(['entries','assignment.simCase']);

        $svc = new SimCheckService();
        $hasErrors = $svc->runHard($chart);

        $this->assertTrue($hasErrors);
        $this->assertDatabaseHas('sim_checks', [
            'chart_id'  => $chart->id,
            'rule_code' => 'MAR_ALLERGY',
            'severity'  => 'error',
        ]);
    }

    /** @test */
    public function ncp_incomplete_is_error_in_hard_mode()
    {
        ['chart' => $chart] = $this->seedMinimalCase();

        // Intentionally incomplete NCP (no interventions)
        SimChartEntry::create([
            'chart_id' => $chart->id,
            'module'   => 'ncp',
            'payload_json' => [
                'dx'    => 'Risk for infection',
                'goals' => 'Afebrile within 24h',
                // 'interventions' missing
            ],
        ]);

        $chart->load(['entries','assignment.simCase']);

        $svc = new SimCheckService();
        $hasErrors = $svc->runHard($chart);

        $this->assertTrue($hasErrors);
        $this->assertDatabaseHas('sim_checks', [
            'chart_id'  => $chart->id,
            'rule_code' => 'NCP_INCOMPLETE',
            'severity'  => 'error',
        ]);
    }
}
