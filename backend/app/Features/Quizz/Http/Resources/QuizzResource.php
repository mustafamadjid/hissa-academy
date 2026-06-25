<?php

namespace App\Features\Quizz\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizzResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'quiz_name' => $this->quiz_name,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
