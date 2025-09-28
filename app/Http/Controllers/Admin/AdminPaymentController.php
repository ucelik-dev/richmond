<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\PaymentDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminPaymentCreateRequest;
use App\Http\Requests\Admin\AdminPaymentUpdateRequest;
use App\Models\Installment;
use App\Models\Payment;
use App\Models\User;
use App\Mail\StudentInstallmentCreatedMail;
use App\Mail\StudentInstallmentUpdatedMail;
use App\Models\EmailLog;
use App\Models\PaymentStatus;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Mail;

class AdminPaymentController extends Controller
{

    public function index(PaymentDataTable $dataTable)
    {
        $payments = Payment::select('payments.*')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->with(['user', 'course.level', 'installments', 'commissions'])
            ->orderBy('users.name', 'asc')
            ->get();

        $lateInstallments = Installment::where('status', '!=', 'paid')
            ->whereDate('due_date', '<', now())
            ->whereHas('payment.user', function ($query) {
                $query->where('account_status', 1); // Only active users
            })
             ->whereHas('payment.paymentStatus', function ($query) {
                $query->where('status_id', 1); // Only ongoing users
            })
            ->with(['payment.user', 'payment.course','payment.paymentStatus'])
            ->get();
        
        // Get count
        $lateInstallmentsCount = (clone $lateInstallments)->count();
        // Get total amount
        $lateInstallmentsAmount = (clone $lateInstallments)->sum('amount');

        return $dataTable->render('admin.payment.index', compact('payments','lateInstallments','lateInstallmentsCount','lateInstallmentsAmount'));
    }

    public function create()
    {
        // Get all (user_id, course_id) pairs that already exist in payments
        $existingPayments = Payment::select('user_id', 'course_id')->get();
        $paymentStatuses = PaymentStatus::where('status', 1)->get();

        // Filter users who are students and have enrollments
        $students = User::with('enrollments')->studentsOnly()
            ->whereHas('enrollments') // must have at least one enrollment
            ->with(['enrollments.course.level']) // eager load course & level
            ->get()
            ->filter(function ($student) use ($existingPayments) {
                // Only return enrollments not already in payments
                $student->enrollments = $student->enrollments->filter(function ($enrollment) use ($existingPayments, $student) {
                    return !$existingPayments->contains(function ($payment) use ($student, $enrollment) {
                        return $payment->user_id === $student->id && $payment->course_id === $enrollment->course_id;
                    });
                });
                return $student->enrollments->isNotEmpty();
            });

        return view('admin.payment.create', compact('students','paymentStatuses'));
    }

    public function store(AdminPaymentCreateRequest $request)
    {

        // Calculate total on the backend from validated amount and discount
        $calculatedTotal = $request->amount - $request->discount;

        // Prevent duplicate payment for same user and course
        $exists = Payment::where('user_id', $request->user_id)
            ->where('course_id', $request->course_id)
            ->exists();

        if ($exists) {
            notyf()->error('A payment already exists for this student and course!');
            return redirect()->back();
        }

        $payment = Payment::create([
            'user_id' => $request->user_id,
            'course_id' => $request->course_id,
            'amount' => $request->amount,
            'discount' => $request->discount ?? 0,
            'total' => $calculatedTotal,
            'status_id' => $request->status_id,
            'notes' => $request->notes,
            'currency' => 'GBP',
        ]);

        notyf()->success('Payment created successfully!');
        return redirect()->route('admin.payment.edit', $payment->id);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $payment = Payment::with('user')->findOrFail($id);
        $installments = $payment->installments;
        $commissions = $payment->commissions;
        $paymentStatuses = PaymentStatus::where('status', 1)->get();
        
        $commissionUserIds = array_filter([
            $payment->user->sales_person_id ?? null,
            $payment->user->agent_id ?? null,
            $payment->user->manager_id ?? null,
        ]);

        $commissionUsers = User::with('mainRoleRelation')->whereIn('id', $commissionUserIds)->get();

        return view('admin.payment.edit', compact('payment','installments','commissions','commissionUsers','paymentStatuses'));
    }

    public function update(AdminPaymentUpdateRequest $request, Payment $payment)
    {
        // 1) Recalculate total and update payment
        $calculatedTotal = $request->amount - $request->discount;

        $payment->update([
            'amount'    => $request->amount,
            'discount'  => $request->discount,
            'total'     => $calculatedTotal,
            'status_id' => $request->status_id,
            'notes'     => $request->notes,
        ]);

        /*
            * OPTIONAL: Server-side recalculation of commissions for security
            *
            * If you want to re-calculate sales_person_commission_amount and agent_commission_amount
            * on the backend to prevent frontend tampering, you would do it here:
            *
            * $salesPersonCommission = 0;
            * if (!empty($validated['sales_person_id'])) {
            * $salesPerson = User::find($validated['sales_person_id']);
            * if ($salesPerson) {
            * $percent = $salesPerson->sales_person_commission_percent ?? 0;
            * $salesPersonCommission = ($calculatedTotal * ($percent / 100));
            * }
            * }
            * $agentCommission = 0;
            * if (!empty($validated['agent_id'])) {
            * $agent = User::find($validated['agent_id']);
            * if ($agent) {
            * $percent = $agent->agent_commission_percent ?? 0;
            * $agentCommission = ($calculatedTotal * ($percent / 100));
            * }
            * }
            *
            * Then, use $salesPersonCommission and $agentCommission in the $payment->update() call below,
            * instead of directly using $validated['sales_person_commission_amount'] etc.
            * You might also add a check to see if the submitted amount is close to the calculated amount.
        */


        // 2) Installments: detect add/update/delete via IDs
        $hadInstallmentsBefore = $payment->installments()->exists();
        $installmentsChanged   = false;
        $affectedInstallments  = collect();

        // small helpers for safe normalization
        $toDate = fn($v) => $v ? Carbon::parse($v)->toDateString() : null;

        DB::transaction(function () use ($request, $payment, &$installmentsChanged, &$affectedInstallments, $toDate) {
            $posted     = collect($request->installments ?? []);
            $postedIds  = $posted->pluck('id')->filter()->map(fn($v) => (int) $v)->all();
            $existing   = $payment->installments()->get();

            // a) DELETE anything not posted back
            $toDelete = $existing->whereNotIn('id', $postedIds);
            if ($toDelete->isNotEmpty()) {
                $payment->installments()->whereIn('id', $toDelete->pluck('id'))->delete();
                $installmentsChanged = true;
            }

            // b) UPSERT posted rows
            foreach ($posted as $row) {
                // normalize incoming values
                $newDue    = $toDate($row['due_date'] ?? null);
                $newPaidAt = (isset($row['status']) && $row['status'] === 'paid' && !empty($row['paid_at']))
                            ? $toDate($row['paid_at']) : null; // ensure null when not paid
                $newAmount = (float)($row['amount'] ?? 0);
                $newStatus = $row['status'] ?? 'pending';
                $newNote   = $row['note'] ?? null;

                $payload = [
                    'due_date' => $newDue,
                    'paid_at'  => $newPaidAt,
                    'amount'   => $newAmount,
                    'status'   => $newStatus,
                    'note'     => $newNote,
                ];

                if (!empty($row['id'])) {
                    // UPDATE existing
                    $inst = $payment->installments()->find((int) $row['id']);
                    if ($inst) {
                        // capture old values (normalized)
                        $oldDue    = $toDate($inst->due_date);
                        $oldPaidAt = $toDate($inst->paid_at);
                        $oldAmount = (float) $inst->amount;
                        $oldStatus = (string) $inst->status;

                        $inst->fill($payload);

                        // save if anything changed at all (keeps DB aligned)
                        $dirty = $inst->isDirty(['due_date','paid_at','amount','status','note']);
                        if ($dirty) {
                            $inst->save();
                        }

                        // mark "installmentsChanged" ONLY if these tracked fields differ
                        if ($dirty && (
                            $oldAmount !== $newAmount ||
                            $oldStatus !== $newStatus ||
                            $oldDue    !== $newDue   ||
                            $oldPaidAt !== $newPaidAt
                        )) {
                            $installmentsChanged = true;
                        }

                        $affectedInstallments->push($inst);
                    }
                } else {
                    // CREATE new
                    $inst = $payment->installments()->create($payload);
                    $affectedInstallments->push($inst);
                    $installmentsChanged = true;
                }
            }
        });

        // 3) Email only if installments actually changed (added/updated/deleted)
        if ($installmentsChanged && $affectedInstallments->isNotEmpty()) {
            $mail = $hadInstallmentsBefore
                ? new StudentInstallmentUpdatedMail($payment, $affectedInstallments)
                : new StudentInstallmentCreatedMail($payment, $affectedInstallments);

            try {
                Mail::to($payment->user->contact_email)->send($mail);
                notyf()->success($hadInstallmentsBefore ? 'Installment update email sent!' : 'Installment create email sent!');
            } catch (\Throwable $e) {
                report($e);
                notyf()->error('Installment email could not be sent!');
            }
        }

        // 4) Commissions (keep your existing behavior)
        $payment->commissions()->delete(); 
        if (isset($request->commissions)) {
            foreach ($request->commissions as $data2) {
                $payment->commissions()->create([
                    'amount' => $data2['amount'],
                    'payment_id' => $payment->id,
                    'user_id'  => $data2['user_id'] ?: null,
                    'payee_name' => !empty($data2['user_id']) ? null : ($data2['payee_name'] ?? null),
                    'status' => $data2['status'],
                    'paid_at' => $data2['status'] === 'paid' ? $data2['paid_at'] : null,
                    'note' => $data2['note'],
                ]);
            }
        }

        notyf()->success('Payment updated successfully!');
        
        if ($request->input('action') === 'save_stay') {
            return redirect()->back();
        } else {
            return redirect()->route('admin.payment.index');
        }
    }

    public function destroy(string $id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->installments()->exists() || $payment->commissions()->exists()) {
            return response(['status' => 'error', 'message' => 'You cannot delete a payment that has installments or commissions!'], 500);
        }

        try {
            $payment->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Payment deleted successfully!'], 200);

        } catch (\Exception $e) {
            notyf()->error('Something went wrong!');
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
