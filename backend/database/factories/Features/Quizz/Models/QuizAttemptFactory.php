<?php

namespace Database\Factories\Features\Quizz\Models;

use App\Features\Quizz\Models\QuizAttempt;
use App\Features\Quizz\Models\Quizz;
use App\Features\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuizAttempt>
 */
class QuizAttemptFactory extends Factory
{
    protected $model = QuizAttempt::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'quizz_id' => Quizz::factory(),
            'status' => 'in_progress',
            'score' => null,
            'started_at' => now(),
            'submitted_at' => null,
        ];
    }
}
