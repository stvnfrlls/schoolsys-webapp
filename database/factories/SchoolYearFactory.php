<?php

namespace Database\Factories;

use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolYear>
 */
class SchoolYearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->unique()->numberBetween(2015, 2035);

        return [
            'name'       => $start . '-' . ($start + 1),
            'start_date' => $start . '-06-01',
            'end_date'   => ($start + 1) . '-03-31',
            'is_active'  => 'active',
        ];
    }
}
