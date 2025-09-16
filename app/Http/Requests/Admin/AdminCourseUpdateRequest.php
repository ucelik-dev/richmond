<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminCourseUpdateRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:255'],
            'credits' => ['required', 'string', 'max:255'],
            'level_id' => ['required','numeric'],
            'awarding_body_id' => ['required','numeric'],
            'category_id' => ['required','numeric'],
            'price' => ['required', 'numeric'],
            'discount' => ['required', 'numeric'],
            'description' => ['required'],
            'overview' => ['required'],
            'overview_details' => ['required'],
            'learning_outcomes' => ['required'],
            'thumbnail' => ['nullable', 'image', 'max:3000'],
            'logo' => ['nullable', 'image', 'max:3000'],
            'handbook_file' => ['nullable', 'file', 'mimes:pdf', 'max:10000'],
            'mapping_document' => ['nullable', 'file', 'mimes:pdf', 'max:10000'],
            'assignment_specification' => ['nullable', 'file', 'mimes:pdf', 'max:10000'],
            'curriculum' => ['nullable', 'file', 'mimes:pdf', 'max:10000'],
            'demo_video_storage' => ['nullable', 'in:youtube,vimeo,external_link', 'string'],
            'demo_video_source' => ['nullable','url'],
            'status' => ['required','in:active,inactive,draft','string'],
            'show_in_select' => ['required','boolean'],
            'completion_test' => ['nullable','boolean'],
            'completion_certificate' => ['nullable','boolean'],
        ];
    }
}
