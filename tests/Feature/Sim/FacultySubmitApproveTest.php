<?php

namespace Tests\Feature\Sim;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{SimCase, Faculty, Admin};

class FacultySubmitApproveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // If you rely on real tables via SQL dumps, remove RefreshDatabase and seed instead.
        // $this->seed();
    }

    public function test_faculty_submits_case_and_admin_approves(): void
    {
        // Arrange
        $faculty = Faculty::factory()->create();   // ensure you have a factory; otherwise, create minimal row
        $admin   = Admin::factory()->create();

        $case = SimCase::create([
            'faculty_id' => $faculty->id,
            'title'      => 'Dummy Case',
            'status'     => 'draft',
            'version'    => 1,
        ]);

        // Faculty submits
        $this->actingAs($faculty, 'faculty')
            ->post(route('faculty.sim.cases.submit', ['case' => $case->id]))
            ->assertRedirect();

        $case->refresh();
        $this->assertEquals('pending_approval', $case->status);

        // Admin approves
        $this->actingAs($admin, 'admin')
            ->post(route('admin.sim.approve', ['case' => $case->id]))
            ->assertRedirect();

        $case->refresh();
        $this->assertEquals('live', $case->status);
        $this->assertNotNull($case->approved_at);
    }
}
