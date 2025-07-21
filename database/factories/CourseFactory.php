<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $semesters = range(1, 6);
        $types = ['ΥΠΟΧΡΕΩΤΙΚΟ', 'ΕΠΙΛΟΓΗΣ'];

        return [
            'name' => $this->faker->words(3, true),
            'code' => 'ΕΠ' . $this->faker->unique()->numberBetween(100, 999),
            'semester' => $this->faker->randomElement($semesters),
            'type' => $this->faker->randomElement($types),
            'description' => $this->faker->optional()->paragraph(),
        ];
    }
}
