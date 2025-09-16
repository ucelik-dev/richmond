<?php

namespace App\Http\Controllers\Frontend\Student;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentPaymentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $payments = Payment::with(['user', 'course.level', 'installments', 'commissions'])
        ->where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();


        return view('frontend.student.payment.index', compact('payments'));
    }
}
