<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminModuleUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'level_id' => ['required', 'exists:course_levels,id'],
            'awarding_body_id' => ['required', 'exists:awarding_bodies,id'],
            'description' => ['required'],
            'overview' => ['required'],
            'learning_outcomes' => ['required'],
            'assignment_file' => ['nullable', 'file', 'mimes:pdf', 'max:10000'],
            'sample_assignment_file' => ['nullable', 'file', 'mimes:pdf', 'max:10000'],
            'video_url' => ['nullable','url'],
            'status' => ['required','boolean'],
            'order' => ['required','numeric'],
        ];
    }
}
