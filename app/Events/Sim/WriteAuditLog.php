<?php
// app/Listeners/Sim/WriteAuditLog.php
namespace App\Listeners\Sim;

use App\Models\SimAuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class WriteAuditLog implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle any simulation event and write a normalized audit row.
     */
    public function handle(object $event): void
    {
        // Map event → type + attributes
        [$type, $attrs] = $this->mapEvent($event);

        SimAuditLog::create([
            'event_type'  => $type,
            'actor_id'    => $attrs['actor_id'] ?? null,
            'actor_role'  => $attrs['actor_role'] ?? null,
            'case_id'     => $attrs['case_id'] ?? null,
            'run_id'      => $attrs['run_id'] ?? null,
            'details_json'=> $attrs['details'] ?? [],
            'created_at'  => now(),
        ]);
    }

    private function mapEvent(object $event): array
    {
        $cls = $event::class;

        // Submitted for approval
        if ($cls === \App\Events\Sim\SimulationSubmittedForApproval::class) {
            return ['case.submitted_for_approval', [
                'actor_id'   => $event->facultyId,
                'actor_role' => 'faculty',
                'case_id'    => $event->case->id,
                'details'    => [
                    'title' => $event->case->title,
                    'status'=> $event->case->status,
                ],
            ]];
        }

        // Approved
        if ($cls === \App\Events\Sim\SimulationApproved::class) {
            return ['case.approved', [
                'actor_id'   => $event->adminId,
                'actor_role' => 'admin',
                'case_id'    => $event->case->id,
                'details'    => [
                    'title' => $event->case->title,
                    'approved_at' => (string) $event->case->approved_at,
                ],
            ]];
        }

        // Rejected
        if ($cls === \App\Events\Sim\SimulationRejected::class) {
            return ['case.rejected', [
                'actor_id'   => $event->adminId,
                'actor_role' => 'admin',
                'case_id'    => $event->case->id,
                'details'    => [
                    'title' => $event->case->title,
                    'note'  => $event->note,
                ],
            ]];
        }

        // Run started
        if ($cls === \App\Events\Sim\SimulationRunStarted::class) {
            return ['run.started', [
                'actor_id'   => $event->run->student_user_id,
                'actor_role' => 'student',
                'case_id'    => $event->run->case_id,
                'run_id'     => $event->run->id,
                'details'    => [
                    'status'     => $event->run->status,
                    'started_at' => (string) $event->run->started_at,
                ],
            ]];
        }

        // Run submitted
        if ($cls === \App\Events\Sim\SimulationRunSubmitted::class) {
            return ['run.submitted', [
                'actor_id'   => $event->run->student_user_id,
                'actor_role' => 'student',
                'case_id'    => $event->run->case_id,
                'run_id'     => $event->run->id,
                'details'    => [
                    'status'        => $event->run->status,
                    'submitted_at'  => (string) $event->run->submitted_at,
                    'completeness'  => $event->completenessMap,
                ],
            ]];
        }

        // Fallback (won’t happen if you only map above)
        return ['unknown', ['details' => ['class' => $cls]]];
    }
}