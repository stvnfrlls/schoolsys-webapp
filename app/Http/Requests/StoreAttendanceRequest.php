<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create attendance');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'schedule_id' => ['required', 'integer', 'exists:schedules,id'],
            'date'        => ['required', 'date'],
            'records'     => ['required', 'array'],
            'records.*'   => ['required', Rule::in(['present', 'late', 'absent', 'excused'])],
            'remarks'     => ['nullable', 'array'],
            'remarks.*'   => ['nullable', 'string', 'max:500'],
        ];
    }
}
