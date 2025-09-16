<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class AdminExpenseCategoryController extends Controller
{
    public function index()
    {
        $expenseCategories = ExpenseCategory::all();
        return view('admin.setting.expense-category.index', compact('expenseCategories'));
    }

    public function create()
    {
        return view('admin.setting.expense-category.create');
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'is_recurring' => 'required|in:0,1',
        ]);

        // Create the awarding body
        ExpenseCategory::create([
            'name' => $request->name,
            'is_recurring' => $request->is_recurring,
        ]);

        notyf()->success('Created successfully!');
        return redirect()->route('admin.setting-expense-category.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $expenseCategory = ExpenseCategory::findOrFail($id);
        return view('admin.setting.expense-category.edit', compact('expenseCategory'));
    }

    public function update(Request $request, string $id)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'is_recurring' => 'required|in:0,1', 
        ]);

        // Find and update the record
        $expenseCategory = ExpenseCategory::findOrFail($id);

        $expenseCategory->update([
            'name' => $request->name,
            'is_recurring' => $request->is_recurring,
        ]);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.setting-expense-category.index');
    }

    public function destroy(string $id)
    {
        try {
            $expenseCategory = ExpenseCategory::findOrFail($id);
            $expenseCategory->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
