<?php

namespace App\Features\Quizz\DTOs;

final readonly class QuizzCreateData
{
    public function __construct(
        public string $quizName,
        public bool $isActive,
    ) {}
}
