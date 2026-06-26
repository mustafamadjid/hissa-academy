<?php

namespace App\Features\Quizz\DTOs;

final readonly class SubmitQuizAttemptData
{
    /**
     * @param  array<int, SubmittedQuizAnswerData>  $answers
     */
    public function __construct(
        public array $answers,
    ) {}
}
