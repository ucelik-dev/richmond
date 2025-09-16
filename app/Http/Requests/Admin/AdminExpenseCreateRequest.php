<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminExpenseCreateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isRecurring = $this->filled(['recurring_start', 'recurring_end']);

        return [
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'note'                => ['nullable', 'string'],
            
            // Single entry fields
            'amount'            => $isRecurring ? ['nullable'] : ['required', 'numeric'],
            'transaction_fee'   => $isRecurring ? ['nullable'] : ['nullable', 'numeric'],
            'expense_date'      => $isRecurring ? ['nullable', 'date'] : ['required', 'date'],
            'status'            => $isRecurring ? ['nullable', 'in:paid,unpaid'] : ['required', 'in:paid,unpaid'],

            // Recurring fields
            'recurring_start'           => ['nullable', 'date_format:Y-m'],
            'recurring_end'             => ['nullable', 'date_format:Y-m', 'after_or_equal:recurring_start'],
            'recurring_amount'          => $isRecurring ? ['required', 'numeric'] : ['nullable', 'numeric'],
            'recurring_transaction_fee' => $isRecurring ? ['nullable', 'numeric'] : ['nullable', 'numeric'],
            'salary_user_id'            => ['nullable', 'exists:users,id'],
            'status'                    => ['required', 'in:paid,unpaid'],
        ];
    }

}
