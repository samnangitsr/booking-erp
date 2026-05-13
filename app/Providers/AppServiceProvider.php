<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Use Bootstrap 5 pagination for all paginators across the app.
        Paginator::useBootstrapFive();

        // Bind Gate to the manual permission system so policies / blade `@can`
        // and the Gate facade work using our roles + permissions tables.
        Gate::before(function ($user, $ability) {
            if (! $user) {
                return null;
            }
            if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return true;
            }
            if (method_exists($user, 'hasPermission') && $user->hasPermission($ability)) {
                return true;
            }

            return null;
        });
    }
}
