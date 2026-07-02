<?php

namespace App\Features\Quizz\DTOs;

final readonly class QuizAccessData
{
    public function __construct(
        public string $courseUuid,
        public string $quizUuid,
        public bool $canAccess,
        public int $requiredLessons,
        public int $completedRequiredLessons,
        public ?string $reason,
    ) {}
}
