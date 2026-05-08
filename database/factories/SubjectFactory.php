<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => fake()->words(3, true),
            'code'        => strtoupper(fake()->unique()->lexify('???###')), // e.g. "MAT101"
            'description' => fake()->optional()->sentence(),
            'is_active'   => 'active',
        ];
    }
}
