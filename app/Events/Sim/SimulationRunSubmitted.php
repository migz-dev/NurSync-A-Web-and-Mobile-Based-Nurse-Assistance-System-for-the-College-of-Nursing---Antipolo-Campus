<?php
// app/Events/Sim/SimulationRunSubmitted.php
namespace App\Events\Sim;

use App\Models\SimRun;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SimulationRunSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(public SimRun $run, public array $completenessMap = []) {}
}
