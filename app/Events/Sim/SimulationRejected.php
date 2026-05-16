<?php

namespace App\Events\Sim;

use App\Models\SimCase;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SimulationRejected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public SimCase $case,
        public int $adminId,
        public string $note
    ) {}
}