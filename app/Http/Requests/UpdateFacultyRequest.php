<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFacultyRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $faculty = $this->route('faculty');

        return [
            // Identity
            'first_name'  => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name'   => ['required', 'string', 'max:100'],

            // Personal
            'birth_date'     => ['nullable', 'date', 'before:today'],
            'gender'         => ['nullable', 'in:male,female'],
            'address'        => ['nullable', 'string', 'max:500'],
            'contact_number' => ['nullable', 'string', 'max:20'],

            // Academic
            'department'     => ['nullable', 'string', 'max:150'],
            'position'       => ['nullable', 'string', 'max:150'],
            'rank'           => ['nullable', 'in:instructor,assistant_professor,associate_professor,professor'],
            'specialization' => ['nullable', 'string', 'max:255'],

            // Employment
            'employment_type' => ['nullable', 'in:full_time,part_time'],
            'status'          => ['required', 'in:active,inactive,retired'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'first_name'      => 'first name',
            'middle_name'     => 'middle name',
            'last_name'       => 'last name',
            'birth_date'      => 'birth date',
            'contact_number'  => 'contact number',
            'employment_type' => 'employment type',
        ];
    }
}
