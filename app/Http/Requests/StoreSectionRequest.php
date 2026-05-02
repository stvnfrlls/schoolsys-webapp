<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSectionRequest extends FormRequest
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
            'grade_level_id' => ['required', 'exists:grade_levels,id'],
            'name'           => ['required', 'string', 'max:255'],
            'room'           => ['nullable', 'string', 'max:255'],
            'capacity'       => ['nullable', 'integer', 'min:1', 'max:65535'],
            'is_active'      => ['required', 'in:active,inactive'],
        ];
    }
}
