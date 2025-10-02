<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminDiscountCouponCreateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $code = $this->input('code');
        if ($code !== null) {
            $code = strtoupper(preg_replace('/\s+/', '', $code));
        }
        $this->merge([
            'code'   => $code,
            'status' => $this->boolean('status', true), // default status = true
        ]);
    }

    public function rules(): array
    {
        return [
            'code'           => ['required','string','max:64','unique:discount_coupons,code','bail'],
            'agent_id'       => ['required','exists:users,id'],
            'discount_type'  => ['required','in:percent,fixed'],
            'discount_value' => ['required','numeric','min:0.01'],
            'max_uses'       => ['nullable','integer','min:1'],
            'min_amount'     => ['nullable','numeric','min:0'],
            'status'         => ['boolean'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            if ($this->input('discount_type') === 'percent' && (float)$this->input('discount_value') > 100) {
                $v->errors()->add('discount_value', 'Percent cannot exceed 100.');
            }
        });
    }

}
