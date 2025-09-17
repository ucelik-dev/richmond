<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class AdminStudentUpdateRequest extends FormRequest
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
            'college_id' => ['required', 'exists:colleges,id'],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string'],
            'phone' => ['required', 'string', 'max:20'], 
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($this->route('student'))],
            'contact_email' => ['nullable', 'string', 'lowercase', 'email', 'max:255'],
            'dob' => ['required', 'date'], 


            'sales_person_id' => ['nullable', 'exists:users,id'],
            'agent_id' => ['nullable', 'exists:users,id'],
            'manager_id' => ['nullable', 'exists:users,id'],
            'reference' => ['nullable', 'string', 'max:255'],
            

            // Address
            'country_id' => ['nullable', 'exists:countries,id'],
            'city' => ['nullable', 'string', 'max:100'],
            'post_code' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],

            // Status
            'user_status_id' => ['required', 'exists:user_statuses,id'],
            'account_status' => ['required', 'boolean'],

            'password' => ['nullable', Password::defaults(), 'confirmed'],

            // Course Enrollments (multiple rows)
            'enrollments' => ['nullable', 'array'],
            'enrollments.*.course_id' => ['required_with:enrollments.*.group_id,enrollments.*.batch_id', 'nullable', 'exists:courses,id'],
            'enrollments.*.group_id' => ['required_with:enrollments.*.course_id', 'nullable', 'exists:groups,id'],
            'enrollments.*.batch_id' => ['required_with:enrollments.*.course_id', 'nullable', 'exists:batches,id'],
            'enrollments.*.id' => ['nullable', 'integer', 'exists:enrollments,id'],

            // Awarding Body Registration (multiple rows)
            'registrations' => ['nullable', 'array'],
            'registrations.*.course_id' => ['required_with:registrations.*.awarding_body_id', 'nullable', 'exists:courses,id'],
            'registrations.*.awarding_body_id' => ['required_with:registrations.*.course_id', 'nullable', 'exists:awarding_bodies,id'],
            'registrations.*.awarding_body_registration_level_id' => ['required_with:registrations.*.awarding_body_id', 'nullable', 'exists:course_levels,id'],
            'registrations.*.awarding_body_registration_number' => ['required_with:registrations.*.awarding_body_id', 'nullable', 'string', 'max:50'],
            'registrations.*.awarding_body_registration_date' => ['required_with:registrations.*.awarding_body_id', 'nullable', 'date'],

            // Documents (array with nested validation)
            'documents' => ['nullable', 'array'],
            'documents.*.category_id' => ['required_with:documents.*.file', 'exists:document_categories,id'],
            'documents.*.file' => ['required_with:documents.*.category_id', 'file', 'max:2048'],
            'documents.*.date' => ['nullable', 'date'],

            // User Notes
            'user_notes' => ['nullable','array'],
            'user_notes.*.id'   => ['nullable','integer','exists:user_notes,id'],
            'user_notes.*.note' => ['nullable','string','max:1000'],


            // Graduates
            'graduations' => ['array'],
            'graduations.*.id'                => ['nullable','integer','exists:graduates,id'],
            'graduations.*.course_id'         => ['nullable','exists:courses,id'],
            'graduations.*.rc_graduation_date'=> ['nullable','date'],
            'graduations.*.top_up_date'       => ['nullable','date'],
            'graduations.*.university'        => ['nullable','string','max:255'],
            'graduations.*.program'           => ['nullable','string','max:255'],
            'graduations.*.study_mode'        => ['nullable','in:online,on_campus,hybrid'],
            'graduations.*.program_entry_date'=> ['nullable','date'],
            'graduations.*.job_status'        => ['nullable','boolean'],
            'graduations.*.job_title'         => ['nullable','string','max:255'],
            'graduations.*.job_start_date'    => ['nullable','date'],
            'graduations.*.note'              => ['nullable','string'],
            'graduations.*.diploma_file'      => ['nullable','file','mimes:pdf','max:20480'], // 20MB
            
        ];
    }

    public function messages()
    {
        return [
            'enrollments.*.batch_id.required_with' => 'Please select a batch when a course is selected.',
            'enrollments.*.group_id.required_with' => 'Please select a group when a course is selected.',
            'enrollments.*.course_id.required_with' => 'Please select a course when assigning a group or batch.',
            'registrations.*.course_id.required_with' => 'Course is required when awarding body is selected.',
            'registrations.*.awarding_body_id.required_with' => 'Awarding body is required when course is selected.',
            'registrations.*.awarding_body_registration_level_id.required_with' => 'Registration level is required when awarding body is selected.',
            'registrations.*.awarding_body_registration_number.required_with' => 'Registration number is required when awarding body is selected.',
            'registrations.*.awarding_body_registration_date.required_with' => 'Registration date is required when awarding body is selected.',
            'graduations.*.course_id' => 'Course is required when saving graduation info.',
        ];
    }

}
