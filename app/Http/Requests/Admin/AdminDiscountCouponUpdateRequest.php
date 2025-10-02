<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminDiscountCouponUpdateRequest extends FormRequest
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
            'active' => $this->boolean('active', (bool)optional($this->route('coupon'))->active),
        ]);
    }

    public function rules(): array
    {
        // Resource route should be {coupon}; if it's {id}, adjust to $this->route('id')
        $coupon = $this->route('coupon'); // model or id
        $couponId = is_object($coupon) ? $coupon->id : $coupon;

        return [
            'code'           => ['prohibited'],
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


