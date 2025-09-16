<?php

namespace App\Http\Controllers\Frontend\Agent;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentRegistrationController extends Controller
{
    function index() {
        $agentId = Auth::id();
        
        // Get all students registered by this agent
        $students = User::with([
            'country',
            'enrollments.course.level',
            'payments.commissions' => function ($query) use ($agentId) {
                $query->where('user_id', $agentId);
            }
        ])
        ->where('agent_id', $agentId)
        ->get();


        $countries = Country::where('status', 1)->get();
        
        return view ('frontend.agent.registration.index', compact('countries','students'));
    }
}
