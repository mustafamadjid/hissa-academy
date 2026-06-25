<?php

namespace App\Features\Quizz\DTOs;

final readonly class QuestionUpdateData
{
    /**
     * @param  array<int, QuestionOptionCreateData>  $answers
     */
    public function __construct(
        public string $question,
        public int $points,
        public int $position,
        public array $answers,
    ) {}
}
