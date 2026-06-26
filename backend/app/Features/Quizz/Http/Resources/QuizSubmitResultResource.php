<?php

namespace App\Features\Quizz\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class QuizSubmitResultResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'attempt_uuid' => $this['attempt']->id,
            'score' => $this['attempt']->score,
            'minimum_score' => (int) $this['attempt']->quiz?->course?->minimum_score,
            'status' => $this['attempt']->status,
            'started_at' => $this['attempt']->started_at?->toISOString(),
            'submitted_at' => $this['attempt']->submitted_at?->toISOString(),
            'result' => [
                'correct_answers' => $this['correct_answers'],
                'incorrect_answers' => $this['incorrect_answers'],
                'total_questions' => $this['total_questions'],
            ],
            'certificate' => $this['certificate'] === null
                ? null
                : new CertificateIssuedResource($this['certificate']),
        ];
    }
}
