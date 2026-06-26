<?php

namespace App\Features\Quizz\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class StudentQuizQuestionResource extends JsonResource
{
    public function __construct(
        mixed $resource,
        private readonly ?string $selectedOptionId = null,
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
            'question_text' => $this->question,
            'points' => (int) $this->points,
            'position' => (int) $this->position,
            'selected_option_uuid' => $this->selectedOptionId,
            'options' => StudentQuizOptionResource::collection($this->answers),
        ];
    }
}
