<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = Auth::user();

        if ($user && $user->hasPermission($permission)) {
            return $next($request);
        }

        // You can customize the unauthorized response
        return abort(403, 'Unauthorized action.');
    }
}
