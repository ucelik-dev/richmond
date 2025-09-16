<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminIncomeUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'income_category_id' => ['required', 'exists:income_categories,id'],
            'note'               => ['nullable', 'string'],
            'amount'             => ['required', 'numeric'],
            'income_date'        => ['required', 'date'],
            'status'             => ['required', 'string'],
        ];
    }

}
