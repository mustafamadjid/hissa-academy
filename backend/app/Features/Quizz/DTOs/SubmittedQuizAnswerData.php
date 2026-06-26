<?php

namespace App\Features\Quizz\DTOs;

final readonly class SubmittedQuizAnswerData
{
    public function __construct(
        public string $questionUuid,
        public string $selectedOptionUuid,
    ) {}
}
