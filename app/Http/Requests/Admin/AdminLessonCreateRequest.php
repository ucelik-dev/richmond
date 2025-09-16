<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminLessonCreateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'modules'   => 'required|array',
            'modules.*' => 'exists:modules,id',
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required'],
            'video_url' => ['nullable','url'],
            'status' => ['required','boolean'],
            'order' => ['required','numeric'],
        ];
    }
}
