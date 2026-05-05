<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
        return [
            // User account fields
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            // Student profile fields
            'student_number'        => ['required', 'string', 'unique:students,student_number'],
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
            'status'                => ['required', 'in:enrolled,graduated,dropped,transferee'],
        ];
    }
}
