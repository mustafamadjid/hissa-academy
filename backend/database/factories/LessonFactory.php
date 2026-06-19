<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
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
            'course_id' => Course::factory(),
            'title' => fake()->sentence(3),
            'position' => fake()->numberBetween(1, 20),
            'is_required' => fake()->boolean(),
        ];
    }
}
