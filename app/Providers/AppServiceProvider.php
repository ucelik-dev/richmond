<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });

        Event::listen(Login::class, function (Login $event) {
            // If you only want to count 'web' guard:
            // if (($event->guard ?? 'web') !== 'web') return;

            $event->user->increment('login_count', 1, [
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);
        });

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
