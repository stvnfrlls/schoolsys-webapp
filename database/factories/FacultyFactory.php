<?php

namespace Database\Factories;

use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faculty>
 */
class FacultyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_number' => $this->faker->unique()->numerify('EMP-####'),
            'first_name'      => $this->faker->firstName(),
            'middle_name'     => $this->faker->optional()->lastName(),
            'last_name'       => $this->faker->lastName(),
            'birth_date'      => $this->faker->date(),
            'gender'          => $this->faker->randomElement(['male', 'female']),
            'employment_type' => 'full_time',
            'status'          => 'active',
        ];
    }
}
