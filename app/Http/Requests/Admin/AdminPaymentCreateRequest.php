<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminPaymentCreateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:enrollments,user_id',
            'course_id' => 'required|exists:enrollments,course_id',
            'amount' => ['required', 'numeric', 'min:0'], 
            'discount' => ['nullable', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'status_id' => 'required|exists:payment_statuses,id',
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
