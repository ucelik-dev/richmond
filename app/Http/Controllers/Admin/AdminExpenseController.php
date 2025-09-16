<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ExpenseDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminExpenseCreateRequest;
use App\Http\Requests\Admin\AdminExpenseUpdateRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Http\Request;

class AdminExpenseController extends Controller
{

    public function index(ExpenseDataTable $dataTable)
    {
        $expenses = Expense::with('expenseCategory')->get();
        return $dataTable->render('admin.expense.index', compact('expenses'));
    }

    public function create()
    {
        $users = User::whereHas('mainRoleRelation', function ($q) {
            $q->whereNotIn('name', ['student', 'agent']);
        })
        ->orderBy('name', 'ASC')
        ->get();

        $expenseCategories = ExpenseCategory::all();
        return view('admin.expense.create', compact('expenseCategories','users'));
    }

    public function store(AdminExpenseCreateRequest $request)
    {
        $category = ExpenseCategory::findOrFail($request->expense_category_id);

        // If category is recurring and recurring fields are filled
        if ($category->is_recurring && $request->filled(['recurring_start', 'recurring_end'])) {
            $start = \Carbon\Carbon::parse($request->recurring_start)->startOfMonth();
            $end = \Carbon\Carbon::parse($request->recurring_end)->startOfMonth();

            while ($start <= $end) {
                $data = [
                    'expense_category_id' => $request->expense_category_id,
                    'note'                => $request->recurring_note,
                    'amount'              => $request->recurring_amount,
                    'transaction_fee'     => $request->recurring_transaction_fee ?? 0,
                    'expense_date'        => $start->toDateString(),
                    'status'              => $request->recurring_status,
                ];

                if ($request->filled('salary_user_id')) {
                    $data['user_id'] = $request->salary_user_id;
                }

                Expense::create($data);
                $start->addMonth();
            }

            notyf()->success('Recurring expenses created successfully.');
            return to_route('admin.expense.index');
        }

        // Single-entry expense
        Expense::create([
            'expense_category_id' => $request->expense_category_id,
            'note'                => $request->note,
            'amount'              => $request->amount,
            'transaction_fee'     => $request->transaction_fee ?? 0,
            'expense_date'        => $request->expense_date,
            'status'              => $request->status,
        ]);

        notyf()->success('Expense created successfully!');
        return to_route('admin.expense.index');
    }



    public function show(string $id)
    {
        //
    }

    public function edit(Expense $expense)
    {
        $expenseCategories = ExpenseCategory::all();
        return view('admin.expense.edit', compact('expense', 'expenseCategories'));
    }

    public function update(AdminExpenseUpdateRequest $request, Expense $expense)
    {
        $expense->update([
            'expense_category_id' =>  $request->expense_category_id,
            'note' => $request->note,
            'amount' => $request->amount,
            'transaction_fee' => $request->transaction_fee,
            'expense_date' => $request->expense_date,
            'status' => $request->status,
        ]);

        notyf()->success('Expense updated successfully!');
        return to_route('admin.expense.index');
    }

    public function destroy(string $id)
    {
        try {
            $expense = Expense::findOrFail($id);
            $expense->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
