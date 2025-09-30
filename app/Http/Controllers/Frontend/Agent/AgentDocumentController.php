<?php

namespace App\Http\Controllers\Frontend\Agent;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentDocumentController extends Controller
{
    public function index()
    {
        // Load the logged-in user with their docs + each doc's category
        $agent = Auth::user()->load('documents.category');

        return view('frontend.agent.document.index', compact('agent'));
    }
}
