<?php

namespace App\Features\Quizz\DTOs;

final readonly class QuestionReorderData
{
    /**
     * @param  array<int, array{id: string, position: int}>  $questions
     */
    public function __construct(
        public array $questions,
    ) {}
}
