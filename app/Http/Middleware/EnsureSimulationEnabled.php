<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSimulationEnabled
{
    public function handle(Request $request, Closure $next)
    {
        // Feature flag lives in config/app.php
        if (! (bool) config('app.simulation_enabled', true)) {
            // If you prefer 404 instead of redirect, use: abort(404);
            return redirect()->route('student.dashboard')
                ->with('error', 'Simulation module is disabled.');
        }

        return $next($request);
    }
}