<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the linked user_id from the student being updated
        $userId = $this->route('student')->user_id;

        return [
            // User account fields
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            // Password is optional on update
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],

            // Student profile fields
            'student_number' => [
                'required',
                'string',
                Rule::unique('students', 'student_number')->ignore($this->route('student')->id),
            ],
            'first_name'            => ['required', 'string', 'max:100'],
            'middle_name'           => ['nullable', 'string', 'max:100'],
            'last_name'             => ['required', 'string', 'max:100'],
            'birth_date'            => ['nullable', 'date', 'before:today'],
            'gender'                => ['nullable', 'in:male,female'],
            'address'               => ['nullable', 'string', 'max:500'],
            'contact_number'        => ['nullable', 'string', 'max:20'],
            'guardian_name'         => ['nullable', 'string', 'max:100'],
            'guardian_contact'      => ['nullable', 'string', 'max:20'],
            'guardian_relationship' => ['nullable', 'string', 'max:50'],
            'status'                => ['required', 'in:active,graduated,inactive'],
        ];
    }
}
