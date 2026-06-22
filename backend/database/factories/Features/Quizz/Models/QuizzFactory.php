<?php

namespace Database\Factories\Features\Quizz\Models;

use App\Features\Course\Models\Course;
use App\Features\Quizz\Models\Quizz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quizz>
 */
class QuizzFactory extends Factory
{
    protected $model = Quizz::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'quiz_name' => fake()->sentence(3),
            'is_active' => fake()->boolean(),
        ];
    }
}
