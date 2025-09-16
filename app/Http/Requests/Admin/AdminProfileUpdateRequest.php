<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminProfileUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image'            => ['nullable','image','max:2048'],
            'name'             => ['required','string','max:255'],
            'gender'           => ['required','in:male,female,other'],
            'phone'            => ['required','string','max:50'],
            'email'            => ['required', 'email', 'max:255', Rule::unique('users')->ignore(auth()->id())],
            'dob'              => ['required','date'],
            'country_id'       => ['nullable','exists:countries,id'],
            'city'             => ['nullable','string','max:255'],
            'post_code'        => ['nullable','string','max:50'],
            'address'          => ['nullable','string','max:500'],
            'password'         => ['nullable','confirmed','min:8'],
        ];
    }

}
