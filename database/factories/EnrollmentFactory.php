<?php

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id'     => Student::factory(),
            'section_id'     => Section::factory(),
            'school_year_id' => SchoolYear::factory(),
            'status'         => 'enrolled',
            'enrolled_at'    => $this->faker->dateTimeThisYear()->format('Y-m-d'),
        ];
    }
}
