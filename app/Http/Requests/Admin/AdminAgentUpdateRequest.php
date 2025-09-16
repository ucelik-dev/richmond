<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminAgentUpdateRequest extends FormRequest
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
            'phone' => ['required', 'string', 'max:100'], 
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($this->route('agent'))],
            'contact_email' => ['nullable', 'string', 'lowercase', 'email', 'max:255'],

            // Address
            'country_id' => ['nullable', 'exists:countries,id'],
            'city' => ['nullable', 'string', 'max:100'],
            'post_code' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],

            // Commission
            'user_id' => ['nullable', 'exists:users,id'],
            'agent_code' => ['nullable', 'string'],
            'commission_percent' => ['nullable', 'integer'],
            'commission_amount' => ['nullable', 'integer'],
            'discount_percent' => ['nullable', 'integer'],
            'discount_amount' => ['nullable', 'integer'],

            // Status
            'user_status_id' => ['required', 'exists:user_statuses,id'],
            'account_status' => ['required', 'boolean'],

            'password' => ['nullable', Password::defaults(), 'confirmed'],

            // Documents (array with nested validation)
            'documents' => ['nullable', 'array'],
            'documents.*.category_id' => ['required_with:documents.*.file', 'exists:document_categories,id'],
            'documents.*.file' => ['required_with:documents.*.category_id', 'file', 'max:10480'],
            'documents.*.date' => ['nullable', 'date'],

            // Social Accounts
            'social_accounts' => ['nullable', 'array'],
            'social_accounts.*.platform_id' => ['required_with:social_accounts.*.link', 'exists:social_platforms,id'],
            'social_accounts.*.link' => ['nullable', 'url', 'max:255'],

            // User Notes
            'user_notes' => ['nullable','array'],
            'user_notes.*.id'   => ['nullable','integer','exists:user_notes,id'],
            'user_notes.*.note' => ['nullable','string','max:1000'],
            
        ];
    }
}
