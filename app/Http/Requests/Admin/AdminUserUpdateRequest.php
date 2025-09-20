<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminUserUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5000'],
            'college_id' => ['required', 'exists:colleges,id'],
            
            // Roles
            'main_role_id' => ['required','integer','exists:roles,id', Rule::in($this->input('roles', [])), ],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', 'exists:roles,id'],

            // Personal
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string'],
            'phone' => ['required', 'string', 'max:20'], 
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($this->route('user')->id)],
            'contact_email' => ['nullable', 'string', 'lowercase', 'email', 'max:255'],
            'dob' => ['required', 'date'], 
            'education_status' => ['nullable', 'string', 'max:100', 'in:high_school_student,high_school_graduate,university_student,university_graduate'],

            // Course Enrollments (multiple rows)
            'enrollments' => ['nullable', 'array'],
            'enrollments.*.course_id' => ['required_with:enrollments.*.group_id,enrollments.*.batch_id', 'nullable', 'exists:courses,id'],
            'enrollments.*.group_id' => ['required_with:enrollments.*.course_id', 'nullable', 'exists:groups,id'],
            'enrollments.*.batch_id' => ['required_with:enrollments.*.course_id', 'nullable', 'exists:batches,id'],
            'enrollments.*.id' => ['nullable', 'integer', 'exists:enrollments,id'],

             // Registration Info
            'sales_person_id' => ['nullable', 'exists:users,id'],
            'agent_id' => ['nullable', 'exists:users,id'],
            'manager_id' => ['nullable', 'exists:users,id'],
            
            // Registration Info
            'sales_person_id' => ['nullable', 'integer', 'exists:users,id'],
            'agent_id' => ['nullable', 'integer', 'exists:users,id'],
            'awarding_body_id' => ['nullable', 'integer', 'exists:awarding_bodies,id'],
            'awarding_body_registration_level' => ['nullable', 'string', 'max:50'],
            'awarding_body_registration_number' => ['nullable', 'string', 'max:50'],
            'awarding_body_registration_date' => ['nullable', 'date'],

            // Address
            'country_id' => ['nullable', 'exists:countries,id'],
            'city' => ['nullable', 'string', 'max:100'],
            'post_code' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],

            // Commission
            'user_id' => ['nullable', 'exists:users,id'],
            'agent_code' => ['nullable', 'string'],
            'commission_percent' => ['nullable', 'integer'],
            'commission_amount' => ['nullable', 'integer'],
            'discount_percent' => ['nullable', 'integer'],
            'discount_amount' => ['nullable', 'integer'],

            // Status
            'user_status_id' => ['required', 'exists:user_statuses,id'],
            'account_status' => ['required', 'boolean'],

            'password' => ['nullable', Password::defaults(), 'confirmed'],

            // Documents (array with nested validation)
            'documents' => ['nullable', 'array'],
            'documents.*.category_id' => ['required_with:documents.*.file', 'exists:document_categories,id'],
            'documents.*.file' => ['required_with:documents.*.category_id', 'file', 'max:2048'],
            'documents.*.date' => ['nullable', 'date'],

            // Social Accounts
            'social_accounts' => ['nullable', 'array'],
            'social_accounts.*.platform_id' => ['required_with:social_accounts.*.link', 'exists:social_platforms,id'],
            'social_accounts.*.link' => ['nullable', 'url', 'max:255'],

            // User Notes
            'user_notes' => ['nullable','array'],
            'user_notes.*.id'   => ['nullable','integer','exists:user_notes,id'],
            'user_notes.*.note' => ['nullable','string','max:1000'],

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
        ];
    }

}
