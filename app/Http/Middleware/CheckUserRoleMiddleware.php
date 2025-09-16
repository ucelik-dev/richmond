<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use Symfony\Component\HttpFoundation\Response;

class CheckUserRoleMiddleware
{
    
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = $request->user();

        // Check if the user has ANY of the roles specified in the middleware
        $hasPermission = false;
        foreach ($user->roles as $userRole) {
            if (in_array($userRole->name, $roles)) {
                $hasPermission = true;
                break;
            }
        }
        
        // If the user does not have any of the required roles, deny access
        if (!$hasPermission) {
            notyf()->error('You do not have permission to view that page!');
            
            $userMainRole = $user->roles()->wherePivot('is_main', true)->first();
            $mainRoleName = strtolower($userMainRole->name ?? '');

            $sharedDashboardRoles = ['admin', 'manager', 'sales'];

            if (in_array($mainRoleName, $sharedDashboardRoles)) {
                return redirect()->route('admin.dashboard');
            } elseif (Route::has($mainRoleName . '.dashboard')) {
                return redirect()->route($mainRoleName . '.dashboard');
            }
            
            return redirect('/');
        }

        return $next($request);
    }

}
