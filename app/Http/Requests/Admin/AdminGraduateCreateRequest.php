<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminGraduateCreateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Graduates
            'user_id'         => ['required','exists:users,id'],
            'course_id'         => ['required','exists:courses,id'],
            'rc_graduation_date'=> ['nullable','date'],
            'top_up_date'       => ['nullable','date'],
            'university'        => ['nullable','string','max:255'],
            'program'           => ['nullable','string','max:255'],
            'study_mode'        => ['nullable','string','in:online,on_campus,hybrid'],
            'program_entry_date'=> ['nullable','date'],
            'job_status'        => ['nullable','boolean'],
            'job_title'         => ['nullable','string','max:255'],
            'job_start_date'    => ['nullable','date'],
            'note'              => ['nullable','string'],
            'diploma_file'      => ['nullable','file','mimes:pdf','max:20480'], // 20MB
        ];
    }

}
