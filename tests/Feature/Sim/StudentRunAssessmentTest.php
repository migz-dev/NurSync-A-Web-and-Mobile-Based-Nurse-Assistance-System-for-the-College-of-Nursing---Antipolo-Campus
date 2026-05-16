<?php

namespace Tests\Feature\Sim;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{SimCase, User, Faculty, SimRun, SimRunAssessment};

class StudentRunAssessmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_starts_run_saves_assessment_and_submits(): void
    {
        $student = User::factory()->create();
        $faculty = Faculty::factory()->create();

        $case = SimCase::create([
            'faculty_id' => $faculty->id,
            'title'      => 'Live Case',
            'status'     => 'live',
            'version'    => 1,
        ]);

        // Start run
        $this->actingAs($student, 'web')
            ->post(route('student.sim.start', ['case' => $case->id]))
            ->assertJsonStructure(['run_id']);

        $run = SimRun::where('case_id', $case->id)->where('student_user_id', $student->id)->first();
        $this->assertNotNull($run);

        // Save assessment
        $payload = ['assessment' => ['general_appearance' => 'awake', 'pain' => 3]];
        $this->actingAs($student, 'web')
            ->post(route('student.sim.assessment.save', ['run' => $run->id]), $payload)
            ->assertJson(['ok' => true]);

        $this->assertTrue(SimRunAssessment::where('run_id', $run->id)->exists());

        // Submit
        $this->actingAs($student, 'web')
            ->post(route('student.sim.submit', ['run' => $run->id]))
            ->assertJson(['ok' => true]);

        $run->refresh();
        $this->assertEquals('submitted', $run->status);
        $this->assertNotNull($run->submitted_at);
    }
}
