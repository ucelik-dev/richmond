<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportAccess;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Str;

class AdminImpersonationController extends Controller
{
    // Create a time-limited token and link
    public function createToken(Request $request, User $user)
    {
        Gate::authorize('impersonate-users');

        $token = Str::random(64);
        $record = SupportAccess::create([
            'target_user_id'       => $user->id,
            'created_by_admin_id'  => Auth::id(),
            'token'                => $token,
            'expires_at'           => now()->addMinutes(15),
        ]);

        // Option A: direct route that consumes token
        $url = route('impersonate.start', ['token' => $token]);

        return back()->with('status', "Impersonation link (15 min): $url");
    }

    // Consume token & start impersonation
    public function start(Request $request, string $token)
    {
        $record = SupportAccess::where('token', $token)->firstOrFail();

        abort_unless($record->isValid(), 403, 'Token invalid or expired');

        // ensure the same admin who created the token is using it
        abort_unless(Auth::user()->id === (int) $record->created_by_admin_id, 403, 'This link is not for you.');

        // mark token as used + capture context
        $record->update([
            'used_at'    => now(),
            'ip'         => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        // remember who to return to
        session([
            'impersonator_id'   => $record->created_by_admin_id,
            'impersonation'     => true,
        ]);

        // log start
        $logId = DB::table('impersonation_logs')->insertGetId([
            'admin_id'              => $record->created_by_admin_id,
            'impersonated_user_id'  => $record->target_user_id,
            'started_at'            => now(),
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);
        session(['impersonation_log_id' => $logId]);

        // switch auth to target user
        Auth::loginUsingId($record->target_user_id);

        // figure out target user's MAIN role
        $mainRole = DB::table('roles')
            ->join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', $record->target_user_id)
            ->where('user_roles.is_main', 1)
            ->value('roles.name');

        $role = strtolower((string) $mainRole);

        // map role -> dashboard route name
        $routeName = match ($role) {
            'admin', 'manager', 'sales' => 'admin.dashboard',
            'student'                   => 'student.dashboard',
            'instructor'                => 'instructor.dashboard',
            'agent'                     => 'agent.dashboard',
            default                     => 'home',
        };

        // if mapped route doesn't exist, fall back to home
        if (! Route::has($routeName)) {
            $routeName = 'home';
        }

        return redirect()->route($routeName)->with('impersonating', true);
    }


    // Stop impersonating and restore admin session
    public function stop(Request $request)
    {
        $adminId = session('impersonator_id');
        abort_unless($adminId, 403, 'Not impersonating.');

        // close log if present
        if ($logId = session('impersonation_log_id')) {
            DB::table('impersonation_logs')->where('id', $logId)->update([
                'ended_at'  => now(),
                'updated_at'=> now(),
            ]);
        }

        // clear flags before switching back
        session()->forget(['impersonator_id', 'impersonation_log_id', 'impersonating']);

        // switch back to the original admin
        Auth::loginUsingId($adminId);

        // send the admin somewhere sensible
        $routeName = 'admin.dashboard';             // your admin dashboard route
        if (! Route::has($routeName)) {
            $routeName = 'home';                    // final fallback
        }

        return redirect()->route($routeName)->with('status', 'Stopped impersonating.');
    }

    public function quickStart(Request $request, \App\Models\User $user)
    {
        // Optional: prevent impersonating yourself
        abort_if($user->id === Auth::user()->id, 403, 'Cannot impersonate yourself.');

        // Only admins (via Gate) reach here
        $token = Str::random(64);

        DB::table('support_accesses')->insert([
            'target_user_id'      => $user->id,
            'created_by_admin_id' => Auth::user()->id,
            'token'               => $token,
            'expires_at'          => now()->addMinutes(15),
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        // Jump straight into impersonation
        return redirect()->route('admin.impersonate.start', ['token' => $token]);
    }

}
