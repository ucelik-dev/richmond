<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminCourseCreateRequest extends FormRequest
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
            'category_id' => ['required','numeric'],
            'awarding_body_id' => ['required','numeric'],
            'price' => ['required', 'numeric'],
            'discount' => ['required', 'numeric'],
            'description' => ['required'],
            'overview' => ['required'],
            'overview_details' => ['required'],
            'learning_outcomes' => ['required'],
            'thumbnail' => ['required', 'image', 'max:3000'],
            'logo' => ['required', 'image', 'max:3000'],
            'handbook_file' => ['required', 'file', 'mimes:pdf', 'max:10000'],
            'mapping_document' => ['required', 'file', 'mimes:pdf', 'max:10000'],
            'assignment_specification' => ['required', 'file', 'mimes:pdf', 'max:10000'],
            'curriculum' => ['required', 'file', 'mimes:pdf', 'max:10000'],
            'demo_video_storage' => ['nullable', 'in:youtube,vimeo,external_link', 'string'],
            'demo_video_source' => ['nullable','url'],
            'status' => ['required', 'in:draft,active,inactive', 'string'],
            'show_in_select' => ['required','boolean'],
            'completion_test' => ['nullable','boolean'],
            'completion_certificate' => ['nullable','boolean'],
        ];
    }
}
