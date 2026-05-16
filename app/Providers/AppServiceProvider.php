<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Tailwind pagination templates
        Paginator::useTailwind();

        // Older MySQL/MariaDB compatibility
        Schema::defaultStringLength(191);

        // Force HTTPS in production if APP_URL is https
        if (app()->environment('production') && str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Auto-notify on faculty status change (only if you use an Eloquent model + observer)
        if (class_exists(\App\Models\Faculty::class) && class_exists(\App\Observers\FacultyObserver::class)) {
            \App\Models\Faculty::observe(\App\Observers\FacultyObserver::class);
        }
    }
}
