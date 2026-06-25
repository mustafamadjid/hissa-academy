<?php

namespace App\Features\Quizz\DTOs;

final readonly class QuestionCreateData
{
    /**
     * @param  array<int, QuestionOptionCreateData>  $answers
     */
    public function __construct(
        public string $question,
        public int $position,
        public ?string $imageUrl,
        public array $answers,
    ) {}
}
