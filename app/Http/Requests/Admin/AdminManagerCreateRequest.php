<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class AdminManagerCreateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5000'],
            
            // Personal
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string'],
            'phone' => ['required', 'string', 'max:20'], 
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'contact_email' => ['nullable', 'string', 'lowercase', 'email', 'max:255'],
            'dob' => ['required', 'date'], 

            // Address
            'country_id' => ['nullable', 'exists:countries,id'],
            'city' => ['nullable', 'string', 'max:100'],
            'post_code' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],

            // Status
            'user_status_id' => ['required', 'exists:user_statuses,id'],
            'account_status' => ['required', 'boolean'],

            'password' => ['required', Password::defaults(), 'confirmed'],

            // Documents (array with nested validation)
            'documents' => ['nullable', 'array'],
            'documents.*.category_id' => ['required_with:documents.*.file', 'exists:document_categories,id'],
            'documents.*.file' => ['required_with:documents.*.category_id', 'file', 'max:2048'],
            'documents.*.date' => ['nullable', 'date'],

            // User Notes
            'user_notes' => ['nullable','array'],
            'user_notes.*.id'   => ['nullable','integer','exists:user_notes,id'],
            'user_notes.*.note' => ['nullable','string','max:1000'],
        ];
    }
}
