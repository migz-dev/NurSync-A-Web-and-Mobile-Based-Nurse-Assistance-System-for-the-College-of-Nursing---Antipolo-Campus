<?php

use App\Models\{SimCase, SimRun, User};
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('student cannot start unassigned case', function (): void {
    config()->set('app.simulation_enabled', true);

    $student = User::factory()->create();
    $case    = SimCase::factory()->create(['status' => 'live']);

    actingAs($student); // default web guard
    post(route('student.sim.start', $case))
        ->assertStatus(403); // blocked by assignment check
});

it('student cannot mutate someone else run', function (): void {
    config()->set('app.simulation_enabled', true);

    $studentA = User::factory()->create();
    $studentB = User::factory()->create();
    $case     = SimCase::factory()->create(['status' => 'live']);

    // allow run for A
    \App\Models\SimAccess::create([
        'case_id'         => $case->id,
        'student_user_id' => $studentA->id,
    ]);

    // A starts the run
    actingAs($studentA);
    post(route('student.sim.start', $case))->assertOk();

    $run = SimRun::first();

    // B attempts to post vitals on A's run -> forbidden
    actingAs($studentB);
    post(route('student.sim.vitals.save', $run), [
        'vitals' => ['bp' => '120/80'],
    ])->assertStatus(403);
});