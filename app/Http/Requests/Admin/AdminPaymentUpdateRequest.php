<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminPaymentUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        if ($this->has('installments')) {
            $filtered = collect($this->installments)
                ->reject(function ($row) {
                    // Skip rows that are completely empty
                    return empty($row['due_date']) && empty($row['amount']) && empty($row['status']);
                })
                ->values(); // reindex array keys

            $this->merge([
                'installments' => $filtered->all(),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0'],
            'discount' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'status_id' => 'required|exists:payment_statuses,id',
            'notes' => ['nullable', 'string', 'max:500'],
            'installments' => ['nullable', 'array'],
            'installments.*.due_date' => ['required', 'date'],
            'installments.*.amount' => ['required', 'numeric', 'min:0'],
            'installments.*.status' => ['required', 'in:pending,paid,failed'],
            'installments.*.paid_at' => ['required_if:installments.*.status,paid', 'nullable', 'date'],
            'installments.*.note' => ['nullable', 'string'],

            'commissions' => ['nullable', 'array'],
            'commissions.*.amount' => ['required', 'numeric', 'min:0'],
            'commissions.*.user_id' => ['nullable', 'integer', 'exists:users,id'],
            'commissions.*.payee_name' => ['nullable', 'string', 'max:255'],
            'commissions.*.status' => ['required', 'in:paid,unpaid'],
            'commissions.*.paid_at' => ['required_if:commissions.*.status,paid', 'nullable', 'date'],
            'commissions.*.note' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            // Enforce: for each commission item, either user_id or payee_name must be present
            $commissions = (array) $this->input('commissions', []);
            foreach ($commissions as $i => $row) {
                $userId = $row['user_id'] ?? null;
                $name   = isset($row['payee_name']) ? trim((string) $row['payee_name']) : '';

                if (empty($userId) && $name === '') {
                    $v->errors()->add("commissions.$i.payee_name", 'Select a user or enter an external name.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'installments.*.paid_at.required_if' => 'The "Paid at" date is required for any paid installment.',
            'commissions.*.paid_at.required_if' => 'The "Paid at" date is required for any paid commission.',
        ];
    }

}
