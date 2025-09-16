<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;

class AdminIncomeCategoryController extends Controller
{

    public function index()
    {
        $incomeCategories = IncomeCategory::all();
        return view('admin.setting.income-category.index', compact('incomeCategories'));
    }

    public function create()
    {
        return view('admin.setting.income-category.create');
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Create the awarding body
        IncomeCategory::create([
            'name' => $request->name,
        ]);

        notyf()->success('Created successfully!');
        return redirect()->route('admin.setting-income-category.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $incomeCategory = IncomeCategory::findOrFail($id);
        return view('admin.setting.income-category.edit', compact('incomeCategory'));
    }

    public function update(Request $request, string $id)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Find and update the record
        $incomeCategory = IncomeCategory::findOrFail($id);

        $incomeCategory->update([
            'name' => $request->name,
        ]);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.setting-income-category.index');
    }

    public function destroy(string $id)
    {
        try {
            $incomeCategory = IncomeCategory::findOrFail($id);
            $incomeCategory->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
