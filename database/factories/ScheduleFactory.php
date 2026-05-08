<?php

namespace Database\Factories;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_year_id' => \App\Models\SchoolYear::factory(),
            'section_id'     => \App\Models\Section::factory(),
            'subject_id'     => \App\Models\Subject::factory(),
            'faculty_id'     => \App\Models\Faculty::factory(),
            'day_of_week'    => $this->faker->numberBetween(1, 5),
            'time_start'     => '08:00',
            'time_end'       => '09:00',
            'room'           => $this->faker->optional()->numerify('Room ###'),
        ];
    }
}
