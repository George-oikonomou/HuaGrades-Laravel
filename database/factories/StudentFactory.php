<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
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
            'it' => 'IT2022' . str_pad($this->faker->unique()->numberBetween(0, 160), 3, '0', STR_PAD_LEFT),
            'full_name' => $this->faker->name(),
            'user_id' => null,
        ];
    }
}
