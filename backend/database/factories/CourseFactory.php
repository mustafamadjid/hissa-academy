<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        return [
            'public_uuid' => (string) Str::uuid(),
            'course_name' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(),
            'minimum_score' => fake()->randomFloat(2, 0, 100),
            'status' => fake()->randomElement(['draft', 'active', 'inactive']),
        ];
    }
}
