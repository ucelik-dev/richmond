<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminExpenseUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_category_id' => ['required','exists:expense_categories,id'],
            'note' => ['nullable','string'],
            'amount' => ['required','numeric','min:0'],
            'transaction_fee' => ['nullable','numeric','min:0'],
            'expense_date' => ['required','date'],
            'status'  => ['required', 'in:paid,unpaid'],
        ];
    }
}
