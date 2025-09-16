<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\CommissionDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminCommissionCreateRequest;
use App\Http\Requests\Admin\AdminCommissionUpdateRequest;
use App\Models\Commission;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCommissionController extends Controller
{

    public function index(CommissionDataTable $dataTable)
    {
        $commissions = Commission::with('payment','user')->get();
        return $dataTable->render('admin.commission.index', compact('commissions'));
    }

    public function create()
    {
        $payments = Payment::with('course','user')->get();
        return view ('admin.commission.create', compact('payments'));
    }

    public function store(AdminCommissionCreateRequest $request)
    {
        // Get the student's payment (pick the most recent if multiple)
        $payment = Payment::where('user_id', $request->customer_id)
            ->latest('id')
            ->first();

        if (!$payment) {
            return back()->withErrors(['customer_id' => 'No payment found for the selected student.'])->withInput();
        }

        // If a real user is selected, prefer user_id; otherwise use external name
        $userId    = $request->filled('user_id') ? (int) $request->user_id : null;
        $payeeName = $userId ? null : (trim((string) $request->payee_name) ?: null);

        // Guard: require at least one of user_id OR payee_name
        if (!$userId && !$payeeName) {
            return back()->withErrors(['payee_name' => 'Select a user or enter an external name.'])->withInput();
        }

        // Only keep paid_at when status is "paid"
        $paidAt = $request->status === 'paid' && $request->filled('paid_at')
            ? $request->paid_at
            : null;

        Commission::create([
            'payment_id' => $payment->id,
            'user_id'    => $userId,          // nullable
            'payee_name' => $payeeName,       // nullable
            'amount'     => $request->amount,
            'status'     => $request->status, // 'paid' | 'unpaid'
            'paid_at'    => $paidAt,          // nullable unless paid
            'note'       => $request->note,
        ]);

        notyf()->success('Commission created successfully!');
        return to_route('admin.commission.index');
    }


    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $commission = Commission::with('payment','user')->findOrFail($id);

        $ids = array_filter([
            optional($commission->payment->user)->sales_person_id,
            optional($commission->payment->user)->agent_id,
            optional($commission->user)->id, // ensure current is present
        ]);

        $users = User::with('mainRoleRelation')  // <-- load role
            ->whereIn('id', $ids)
            ->get();

        return view('admin.commission.edit', compact('commission','users'));
    }

    public function update(AdminCommissionUpdateRequest $request, Commission $commission)
    {
        // Prefer user_id if provided; otherwise use external name
        $userId    = $request->filled('user_id') ? (int) $request->user_id : null;
        $payeeName = $userId ? null : (trim((string) $request->payee_name) ?: null);

        // Only keep paid_at when status is 'paid'
        $paidAt = $request->status === 'paid' && $request->filled('paid_at')
            ? $request->paid_at
            : null;

        $commission->update([
            'user_id'    => $userId,      // nullable
            'payee_name' => $payeeName,   // nullable
            'amount'     => $request->amount,
            'status'     => $request->status, // 'paid' | 'unpaid'
            'paid_at'    => $paidAt,          // null unless paid
            'note'       => $request->note,
        ]);

        notyf()->success('Commission updated successfully!');
        return to_route('admin.commission.index');
    }

    public function destroy(string $id)
    {
        try {
            $commission = Commission::findOrFail($id);
            $commission->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
