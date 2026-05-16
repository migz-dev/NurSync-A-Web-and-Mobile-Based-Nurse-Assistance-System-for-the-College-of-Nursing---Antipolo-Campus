<?php

use App\Models\{SimCase, SimRun, User, Faculty};
use Illuminate\Foundation\Testing\RefreshDatabase;

// ✅ Pest Laravel helpers
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('faculty submits, admin approves, student completes run', function (): void {
    // Ensure feature flag middleware allows sim routes
    config()->set('app.simulation_enabled', true);

    /** @var Faculty $faculty */
    $faculty = Faculty::factory()->create();
    $admin   = \App\Models\Admin::factory()->create();
    $student = User::factory()->create();

    // create draft case
    $case = SimCase::factory()->create([
        'faculty_id' => $faculty->id,
        'status'     => 'draft',
    ]);

    // faculty submits
    actingAs($faculty, 'faculty');
    post(route('faculty.sim.cases.submit', $case))->assertRedirect();

    $case->refresh();
    expect($case->status)->toBe('pending_approval');

    // admin approves
    actingAs($admin, 'admin');
    post(route('admin.sim.approve', $case))->assertRedirect();

    $case->refresh();
    expect($case->status)->toBe('live');

    // assign case to student
    \App\Models\SimAccess::create([
        'case_id'         => $case->id,
        'student_user_id' => $student->id,
    ]);

    // student starts run
    actingAs($student); // default web guard
    post(route('student.sim.start', $case))->assertOk();

    $run = SimRun::first();
    expect($run)->not()->toBeNull();
    expect($run->status)->toBe('in_progress');

    // save assessment
    post(route('student.sim.assessment.save', $run), [
        'assessment' => ['avpu' => 'A'],
    ])->assertOk();

    // submit run
    post(route('student.sim.submit', $run))->assertOk();

    $run->refresh();
    expect($run->status)->toBe('submitted');
});