<?php

namespace App\Http\Controllers\Frontend\Agent;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgentDashboardController extends Controller
{
    function index() {
        $agentId = Auth::user()->id;

        $now = Carbon::now();

        // Registrations
        $agentRegistrations = DB::table('users')
        ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
        ->where('users.agent_id', $agentId)
        ->where('user_roles.role_id', 2)
        ->where('user_roles.is_main', 1);

        // Registrations This Month
        $agentRegistrationThisMonth = (clone $agentRegistrations)
            ->whereBetween('users.created_at', [$now->startOfMonth(), $now->copy()->endOfMonth()])
            ->count();

        // Registrations This Year
        $agentRegistrationThisYear = (clone $agentRegistrations)
            ->whereBetween('users.created_at', [$now->startOfYear(), $now->copy()->endOfYear()])
            ->count();

        $agentCommissions = Commission::where('user_id', $agentId)
        ->sum('amount');

        $agentCommissionsPaid = Commission::where('user_id', $agentId)
        ->where('status', 'paid')
        ->sum('amount');

        $agentCommissionsUnpaid = Commission::where('user_id', $agentId)
        ->where('status', 'unpaid')
        ->sum('amount');

        $agentCommissionThisMonth = Commission::where('user_id', $agentId)
            ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->sum('amount');

        $agentCommissionThisYear = Commission::where('user_id', $agentId)
            ->whereBetween('created_at', [$now->copy()->startOfYear(), $now->copy()->endOfYear()])
            ->sum('amount');
        
        $agent = Auth::user()->load('agentProfile');

        return view('frontend.agent.dashboard', compact(
            'agentRegistrations',
            'agentRegistrationThisMonth',
            'agentRegistrationThisYear',
            'agentCommissions',
            'agentCommissionsPaid',
            'agentCommissionsUnpaid',
            'agentCommissionThisMonth',
            'agentCommissionThisYear',
            'agent'
        ));
    }
}
