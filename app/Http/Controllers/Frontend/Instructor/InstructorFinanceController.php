<?php

namespace App\Http\Controllers\Frontend\Instructor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorFinanceController extends Controller
{
    public function index()
    {
        $instructor = User::findOrFail(Auth::user()->id);
        return view('frontend.instructor.finance.index', compact('instructor'));
    }

    public function update(Request $request)
    {
        // Validate (both optional)
        $data = $request->validate([
            'bank_account'    => ['sometimes','nullable','string','max:5000','bail'],
            'invoice_data' => ['sometimes','nullable','string','max:5000','bail'],
        ]);

        // Sanitize helper: trim, keep newlines, strip HTML, empty => null
        $clean = function ($v) {
            if ($v === null) return null;
            $v = preg_replace("/[ \t]+\n/", "\n", trim((string)$v)); // tidy line endings
            $v = strip_tags($v);
            return $v === '' ? null : $v;
        };

        // Only update keys that were actually submitted
        $updates = [];
        if ($request->has('bank_account')) {
            $updates['bank_account'] = $clean($data['bank_account'] ?? null);
        }
        if ($request->has('invoice_data')) {
            $updates['invoice_data'] = $clean($data['invoice_data'] ?? null);
        }

        if ($updates) {
            $user = $request->user();         
            $user->fill($updates)->save();     
        }

        notyf()->success('Finance details updated successfully!');
        return redirect()->back();
    }

}
