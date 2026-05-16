<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSimulationEnabled
{
    public function handle(Request $request, Closure $next)
    {
        if (!config('app.simulation_enabled', (bool) env('SIMULATION_ENABLED', true))) {
            // HTML requests → friendly page; JSON → uniform error
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'error' => 'Simulation module disabled'], 503);
            }
            abort(503, 'Simulation module is currently disabled.');
        }
        return $next($request);
    }
}
