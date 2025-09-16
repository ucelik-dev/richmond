<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\PaymentStatus;
use Illuminate\Http\Request;

class AdminPaymentStatusController extends Controller
{
    
    public function index()
    {
        $paymentStatuses = PaymentStatus::all();
        return view('admin.setting.payment-status.index', compact('paymentStatuses'));
    }

    public function create()
    {
        return view('admin.setting.payment-status.create');
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ]);

        // Create the awarding body
        PaymentStatus::create([
            'name' => $request->name,
            'color' => $request->color,
            'status' => $request->status,
        ]);

        notyf()->success('Created successfully!');
        return redirect()->route('admin.setting-payment-status.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $paymentStatus = PaymentStatus::findOrFail($id);
        return view('admin.setting.payment-status.edit', compact('paymentStatus'));
    }

    public function update(Request $request, string $id)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'status' => 'required|in:0,1', 
        ]);

        // Find and update the record
        $paymentStatus = PaymentStatus::findOrFail($id);

        $paymentStatus->update([
            'name' => $request->name,
            'color' => $request->color,
            'status' => $request->status,
        ]);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.setting-payment-status.index');
    }

    public function destroy(string $id)
    {
        try {
            $paymentStatus = PaymentStatus::findOrFail($id);
            $paymentStatus->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
