<?php
// app/Events/Sim/SimulationRunStarted.php
namespace App\Events\Sim;

use App\Models\SimRun;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SimulationRunStarted
{
    use Dispatchable, SerializesModels;

    public function __construct(public SimRun $run) {}
}
