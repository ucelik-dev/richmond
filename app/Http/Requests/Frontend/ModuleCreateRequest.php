<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class ModuleCreateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required'],
            'overview' => ['required'],
            'learning_outcomes' => ['required'],
            'video_url' => ['nullable','url'],
            'status' => ['required','boolean'],
            'order' => ['required','numeric'],
        ];
    }

}
