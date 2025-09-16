<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class CourseUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'extended_title' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric'],
            'description' => ['required'],
            'overview' => ['required'],
            'learning_outcomes' => ['required'],
            'thumbnail' => ['nullable', 'image', 'max:3000'],
            'logo' => ['nullable', 'image', 'max:3000'],
            'handbook_file' => ['nullable', 'file', 'mimes:pdf', 'max:10000'],
            'mapping_document' => ['nullable', 'file', 'mimes:pdf', 'max:10000'],
            'assignment_specification' => ['nullable', 'file', 'mimes:pdf', 'max:10000'],
            'demo_video_storage' => ['nullable', 'in:youtube,vimeo,external_link', 'string'],
            'demo_video_source' => ['nullable','url'],
            'status' => ['required','in:active,inactive,draft','string'],
            'show_in_select' => ['required','boolean'],
            'completion_test' => ['nullable','boolean'],
            'completion_certificate' => ['nullable','boolean'],
        ];
    }
}
