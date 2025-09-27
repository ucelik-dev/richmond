<?php

namespace App\Http\Requests\Frontend\Agent;

use Illuminate\Foundation\Http\FormRequest;

class AgentSocialUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data.socialPlatforms' => ['nullable', 'array'],
            'data.socialPlatforms.*.social_platform_id' => ['required', 'exists:social_platforms,id'],
            'data.socialPlatforms.*.link' => ['nullable', 'url', 'max:255']
        ];
    }
}
