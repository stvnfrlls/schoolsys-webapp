<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnrollmentRequest extends FormRequest
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
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
                Rule::unique('enrollments')
                    ->where(fn($query) => $query->where('school_year_id', $this->input('school_year_id'))),
            ],
            'section_id'     => ['required', 'integer', 'exists:sections,id'],
            'school_year_id' => ['required', 'integer', 'exists:school_years,id'],
            'status'         => ['sometimes', Rule::in(['enrolled', 'dropped', 'transferred', 'completed'])],
            'enrolled_at'    => ['required', 'date'],
        ];
    }
}
