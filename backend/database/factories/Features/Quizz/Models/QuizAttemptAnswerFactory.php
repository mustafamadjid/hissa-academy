<?php

namespace Database\Factories\Features\Quizz\Models;

use App\Features\Quizz\Models\Answer;
use App\Features\Quizz\Models\Question;
use App\Features\Quizz\Models\QuizAttempt;
use App\Features\Quizz\Models\QuizAttemptAnswer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuizAttemptAnswer>
 */
class QuizAttemptAnswerFactory extends Factory
{
    protected $model = QuizAttemptAnswer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quiz_attempt_id' => QuizAttempt::factory(),
            'question_id' => Question::factory(),
            'answer_option_id' => Answer::factory(),
            'is_correct' => false,
        ];
    }
}
