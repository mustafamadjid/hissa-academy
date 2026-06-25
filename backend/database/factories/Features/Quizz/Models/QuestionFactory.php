<?php

namespace Database\Factories\Features\Quizz\Models;

use App\Features\Quizz\Models\Question;
use App\Features\Quizz\Models\Quizz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quizz_id' => Quizz::factory(),
            'question' => fake()->sentence(),
            'position' => fake()->numberBetween(1, 20),
            'image_url' => fake()->optional()->imageUrl(),
        ];
    }
}
