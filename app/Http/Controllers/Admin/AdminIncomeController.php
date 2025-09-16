<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\IncomeDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminIncomeCreateRequest;
use App\Http\Requests\Admin\AdminIncomeUpdateRequest;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\User;
use Illuminate\Http\Request;

class AdminIncomeController extends Controller
{
    
    public function index(IncomeDataTable $dataTable)
    {
        $incomes = Income::with('incomeCategory')->get();
        return $dataTable->render('admin.income.index', compact('incomes'));
    }

    public function create()
    {
        $incomeCategories = IncomeCategory::all();
        return view('admin.income.create', compact('incomeCategories'));
    }

    public function store(AdminIncomeCreateRequest $request)
    {
        Income::create([
            'income_category_id' => $request->income_category_id,
            'note'               => $request->note,
            'amount'             => $request->amount,
            'income_date'        => $request->income_date,
            'status'             => $request->status,
        ]);

        notyf()->success('Income created successfully!');
        return to_route('admin.income.index');
    }



    public function show(string $id)
    {
        //
    }

    public function edit(Income $income)
    {
        $incomeCategories = IncomeCategory::all();
        return view('admin.income.edit', compact('income', 'incomeCategories'));
    }

    public function update(AdminIncomeUpdateRequest $request, Income $income)
    {
        $income->update([
            'income_category_id' =>  $request->income_category_id,
            'note' => $request->note,
            'amount' => $request->amount,
            'income_date' => $request->income_date,
            'status' => $request->status,
        ]);

        notyf()->success('Income updated successfully!');
        return to_route('admin.income.index');
    }

    public function destroy(string $id)
    {
        try {
            $income = Income::findOrFail($id);
            $income->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
