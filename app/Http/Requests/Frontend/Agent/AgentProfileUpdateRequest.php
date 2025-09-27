<?php

namespace App\Http\Requests\Frontend\Agent;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AgentProfileUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'], // validate against allowed values
            'phone' => ['required', 'string', 'max:20'], // restrict to reasonable length
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore(auth()->id())],
            'dob' => ['nullable', 'date'],  
            'post_code' => ['nullable', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:100'],
            'country_id' => ['required', 'exists:countries,id'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
