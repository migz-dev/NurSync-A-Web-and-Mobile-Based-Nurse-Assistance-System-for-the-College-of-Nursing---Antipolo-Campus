<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // Case lifecycle
        \App\Events\Sim\SimulationSubmittedForApproval::class => [
            \App\Listeners\Sim\WriteAuditLog::class,
        ],
        \App\Events\Sim\SimulationApproved::class => [
            \App\Listeners\Sim\WriteAuditLog::class,
        ],
        \App\Events\Sim\SimulationRejected::class => [
            \App\Listeners\Sim\WriteAuditLog::class,
        ],

        // Student run lifecycle
        \App\Events\Sim\SimulationRunStarted::class => [
            \App\Listeners\Sim\WriteAuditLog::class,
        ],
        \App\Events\Sim\SimulationRunSubmitted::class => [
            \App\Listeners\Sim\WriteAuditLog::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}