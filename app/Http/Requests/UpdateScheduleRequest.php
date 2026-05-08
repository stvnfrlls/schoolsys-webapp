<?php

namespace App\Http\Requests;

use App\Models\Schedule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateScheduleRequest extends FormRequest
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
            'school_year_id' => ['required', 'integer', 'exists:school_years,id'],
            'section_id'     => ['required', 'integer', 'exists:sections,id'],
            'subject_id'     => ['required', 'integer', 'exists:subjects,id'],
            'faculty_id'     => ['required', 'integer', 'exists:faculties,id'],
            'day_of_week'    => ['required', 'integer', 'between:1,6'],
            'time_start'     => ['required', 'date_format:H:i'],
            'time_end'       => ['required', 'date_format:H:i', 'after:time_start'],
            'room'           => ['nullable', 'string', 'max:100'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->hasAny([
                'school_year_id',
                'section_id',
                'faculty_id',
                'day_of_week',
                'time_start',
                'time_end',
            ])) {
                return;
            }

            /** @var Schedule $schedule */
            $schedule = $this->route('schedule');

            $conflict = Schedule::hasConflict(
                dayOfWeek: (int) $this->day_of_week,
                timeStart: $this->time_start,
                timeEnd: $this->time_end,
                schoolYearId: (int) $this->school_year_id,
                facultyId: (int) $this->faculty_id,
                sectionId: (int) $this->section_id,
                room: $this->room,
                excludeId: $schedule->id,   // exclude self from conflict check
            );

            if ($conflict) {
                $validator->errors()->add(
                    'time_start',
                    'A scheduling conflict was detected. The faculty, section, or room already has an overlapping schedule on this day.'
                );
            }
        });
    }

    public function attributes(): array
    {
        return [
            'school_year_id' => 'school year',
            'section_id'     => 'section',
            'subject_id'     => 'subject',
            'faculty_id'     => 'faculty',
            'day_of_week'    => 'day',
            'time_start'     => 'start time',
            'time_end'       => 'end time',
            'room'           => 'room',
        ];
    }

    public function messages(): array
    {
        return [
            'time_end.after'         => 'The end time must be later than the start time.',
            'day_of_week.between'    => 'The selected day must be between Monday and Saturday.',
        ];
    }
}
