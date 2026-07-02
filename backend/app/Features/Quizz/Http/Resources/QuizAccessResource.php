<?php

namespace App\Features\Quizz\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class QuizAccessResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'course_uuid' => $this->courseUuid,
            'quiz_uuid' => $this->quizUuid,
            'can_access' => $this->canAccess,
            'quizPassed' => $this->quizPassed,
            'required_lessons' => $this->requiredLessons,
            'completed_required_lessons' => $this->completedRequiredLessons,
            'reason' => $this->reason,
        ];
    }
}
