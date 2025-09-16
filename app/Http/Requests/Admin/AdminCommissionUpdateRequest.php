<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminCommissionUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Payee (one of these must be present)
            'user_id'    => ['nullable', 'integer', 'exists:users,id'],
            'payee_name' => ['nullable', 'string', 'max:255'],

            // Core fields
            'amount'  => ['required', 'numeric', 'min:0'],
            'status'  => ['required', 'in:paid,unpaid'],
            'paid_at' => ['nullable', 'date', 'required_if:status,paid'],

            'note'    => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $userId = $this->input('user_id');
            $name   = trim((string) $this->input('payee_name'));

            // Require at least one: user_id OR external name
            if (empty($userId) && $name === '') {
                $v->errors()->add('payee_name', 'Select a user or enter an external name.');
            }
        });
    }
    
}
