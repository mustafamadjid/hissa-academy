<?php

namespace App\Features\Quizz\DTOs;

final readonly class QuestionOptionCreateData
{
    public function __construct(
        public string $answer,
        public bool $isCorrect,
    ) {}
}
