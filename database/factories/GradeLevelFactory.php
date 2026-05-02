<?php

namespace Database\Factories;

use App\Models\GradeLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GradeLevel>
 */
class GradeLevelFactory extends Factory
{
    protected $model = GradeLevel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'      => 'Grade ' . $this->faker->unique()->numberBetween(1, 12),
            'level'     => $this->faker->numberBetween(1, 2),
            'is_active' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }

    /**
     * State: active grade level.
     */
    public function active(): static
    {
        return $this->state(['is_active' => 'active']);
    }

    /**
     * State: inactive grade level.
     */
    public function inactive(): static
    {
        return $this->state(['is_active' => 'inactive']);
    }
}
