<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('impersonate-users', function ($user) {
            // Allow only if the user's MAIN role is role_id = 1 (Admin)
            return DB::table('user_roles')
                ->where('user_id', $user->id)
                ->where('is_main', 1)
                ->where('role_id', 1)   // <-- your Admin role_id
                ->exists();
        });

        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($ability) ?: null;
            }
        });
    }
}
