<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_number' => $this->faker->unique()->numerify('STU-#####'),
            'first_name'     => $this->faker->firstName(),
            'middle_name'    => $this->faker->optional()->lastName(),
            'last_name'      => $this->faker->lastName(),
            'birth_date'     => $this->faker->date(),
            'gender'         => $this->faker->randomElement(['male', 'female']),
            'address'        => $this->faker->address(),
            'contact_number' => $this->faker->phoneNumber(),
            'guardian_name'  => $this->faker->name(),
            'guardian_contact'      => $this->faker->phoneNumber(),
            'guardian_relationship' => $this->faker->randomElement(['parent', 'sibling', 'relative']),
            'status'         => 'active',
        ];
    }
}
