<?php

namespace App\Listeners\Sim;

use Illuminate\Support\Facades\Log;
use App\Events\Sim\{
    SimulationSubmittedForApproval,
    SimulationApproved,
    SimulationRejected
};

class LogSimulationEvent
{
    public function handle(object $event): void
    {
        $payload = match (true) {
            $event instanceof SimulationSubmittedForApproval =>
                ['event' => 'submitted_for_approval', 'case_id' => $event->case->id, 'faculty_id' => $event->facultyId],
            $event instanceof SimulationApproved =>
                ['event' => 'approved', 'case_id' => $event->case->id, 'admin_id' => $event->adminId],
            $event instanceof SimulationRejected =>
                ['event' => 'rejected', 'case_id' => $event->case->id, 'admin_id' => $event->adminId, 'note' => $event->note],
            default => ['event' => 'unknown'],
        };

        Log::info('[SIM] '.$payload['event'], $payload);
    }
}