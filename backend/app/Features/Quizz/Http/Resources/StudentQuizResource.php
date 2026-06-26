<?php

namespace App\Features\Quizz\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class StudentQuizResource extends JsonResource
{
    public function __construct(
        mixed $resource,
        private readonly int $attemptsUsed,
    ) {
        parent::__construct($resource);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->id,
            'name' => $this->quiz_name,
            'is_active' => (bool) $this->is_active,
            'minimum_score' => (int) $this->course?->minimum_score,
            'total_questions' => $this->questions->count(),
            'attempt_policy' => [
                'maximum_attempts' => null,
                'attempts_used' => $this->attemptsUsed,
                'attempts_remaining' => null,
            ],
        ];
    }
}
