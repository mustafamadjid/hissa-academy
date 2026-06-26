<?php

namespace App\Features\Quizz\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class QuizAttemptResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $selectedAnswers = $this->answers->keyBy('question_id');

        return [
            'uuid' => $this->id,
            'quiz' => [
                'uuid' => $this->quiz?->id,
                'name' => $this->quiz?->quiz_name,
            ],
            'status' => $this->status,
            'score' => $this->score,
            'started_at' => $this->started_at?->toISOString(),
            'submitted_at' => $this->submitted_at?->toISOString(),
            'questions' => $this->quiz?->questions
                ? $this->quiz->questions
                    ->sortBy('position')
                    ->values()
                    ->map(fn ($question): StudentQuizQuestionResource => new StudentQuizQuestionResource(
                        $question,
                        $selectedAnswers->get($question->id)?->answer_option_id,
                    ))
                : [],
        ];
    }
}
