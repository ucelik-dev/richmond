<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminRecruitmentCreateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'], 
            'phone' => ['required', 'string', 'max:50'], 
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'country_id' => ['nullable', 'exists:countries,id'],
            'source_id' => ['required', 'exists:recruitment_sources,id'],
            'status_id' => ['required', 'exists:recruitment_statuses,id']
        ];
    }
    
}
