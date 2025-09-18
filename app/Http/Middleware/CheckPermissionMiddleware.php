<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $arg1, ?string $arg2 = null)
    {
        $user = $request->user();
        abort_unless($user, 403);

        $ok = $arg2
            ? $user->canResource($arg1, $arg2)  // perm:resource,ability
            : $user->hasPermission($arg1);      // legacy: perm:ability_resource

        abort_unless($ok, 403, 'Unauthorized action.');
        return $next($request);
    }
}
