<?php

namespace App\Features\Quizz\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quizz_id' => $this->quizz_id,
            'question' => $this->question,
            'position' => (int) $this->position,
            'image_url' => $this->image_url,
            'answers' => AnswerResource::collection($this->whenLoaded('answers')),
        ];
    }
}
