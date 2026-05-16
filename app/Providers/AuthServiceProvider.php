<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

// ------------------------------------------------------------
//  SIMULATION MODELS + POLICIES
// ------------------------------------------------------------
use App\Models\{
    SimCase,
    SimRun,
    SimPatient
};

use App\Policies\{
    SimCasePolicy,
    SimRunPolicy,
    SimPatientPolicy
};

// ------------------------------------------------------------
//  CLINICAL EXPERIENCE MODELS + POLICIES
// ------------------------------------------------------------
use App\Models\ClinicalExperience;
use App\Policies\ClinicalExperiencePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [

        /*
        |--------------------------------------------------------------------------
        | Simulation Module Policies
        |--------------------------------------------------------------------------
        */
        SimCase::class        => SimCasePolicy::class,
        SimRun::class         => SimRunPolicy::class,
        SimPatient::class     => SimPatientPolicy::class,

        /*
        |--------------------------------------------------------------------------
        | Clinical Experiences (CI → My Clinical Experience)
        |--------------------------------------------------------------------------
        */
        ClinicalExperience::class => ClinicalExperiencePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Registers all policies defined above
        $this->registerPolicies();

        // Additional gates may be defined here if needed
        // Gate::define('view-admin-dashboard', fn ($user) => $user->isAdmin());
    }
}
