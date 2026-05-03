<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubjectPerLevelRequest extends FormRequest
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
            'gradelevel_id' => [
                'required',
                'integer',
                'exists:grade_levels,id',
            ],
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
                Rule::unique('subject_per_levels')->where('gradelevel_id', $this->gradelevel_id),
            ],
            'hours_per_week' => [
                'nullable',
                'numeric',
                'min:0',
                'max:168',
            ],
            'is_active' => [
                'required',
                'in:active,inactive'
            ],
        ];
    }
}
