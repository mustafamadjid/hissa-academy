<?php

namespace Database\Factories\Features\Course\Models;

use App\Features\Course\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_name' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(),
            'minimum_score' => fake()->randomFloat(2, 0, 100),
            'status' => fake()->randomElement(['draft', 'active', 'inactive']),
        ];
    }
}
