<?php

namespace Database\Factories\Features\Quizz\Models;

use App\Features\Quizz\Models\Answer;
use App\Features\Quizz\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Answer>
 */
class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'answer' => fake()->sentence(),
            'is_correct' => fake()->boolean(),
        ];
    }
}
