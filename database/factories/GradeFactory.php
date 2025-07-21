<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Grade>
 */
class GradeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::inRandomOrder()->first()?->id ?? Student::factory(),
            'course_id' => Course::inRandomOrder()->first()?->id ?? Course::factory(),
            'grade' => $this->faker->numberBetween(0, 10),
        ];
    }

    public function passed(): static
    {
        return $this->state(fn (array $attributes) => [
            'grade' => $this->faker->numberBetween(5, 10), // passing grades: 5 to 10
        ]);
    }

    public function notPassed(): static
    {
        return $this->state(fn (array $attributes) => [
            'grade' => $this->faker->numberBetween(0, 4), // failing grades: below 5
        ]);
    }
}
