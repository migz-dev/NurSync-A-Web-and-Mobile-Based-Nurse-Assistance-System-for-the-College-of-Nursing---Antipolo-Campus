<?php

namespace App\Providers;

use App\Models\Procedure;
use App\Models\ReturnDemoSkill;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /** Post-login default */
    public const HOME = '/student/dashboard';

    public function boot(): void
    {
        /* =================== Rate Limiting =================== */
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email');

            // 5 attempts per minute per (email + IP)
            return [
                Limit::perMinute(5)->by($email.'|'.$request->ip()),
            ];
        });

        /* =================== Route Patterns =================== */
        // Accept slugs like "iv-cannulation" and also plain ids if ever used
        Route::pattern('procedure', '[A-Za-z0-9\-_]+');
        Route::pattern('skill', '[A-Za-z0-9\-_]+');

        /* ============ Implicit Model Bindings (slug-first) ============ */
        // {procedure} → Procedure by slug, fallback to id
        Route::bind('procedure', function ($value) {
            return Procedure::query()
                ->where('slug', $value)
                ->when(is_numeric($value), fn($q) => $q->orWhere('id', (int) $value))
                ->firstOrFail();
        });

        // {skill} → ReturnDemoSkill by slug, fallback to id
        Route::bind('skill', function ($value) {
            return ReturnDemoSkill::query()
                ->where('slug', $value)
                ->when(is_numeric($value), fn($q) => $q->orWhere('id', (int) $value))
                ->firstOrFail();
        });

        /* =================== Route Files =================== */
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
