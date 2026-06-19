<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Quizz;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Quizz>
 */
class QuizzFactory extends Factory
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
            'quiz_name' => fake()->sentence(3),
            'is_active' => fake()->boolean(),
        ];
    }
}
