<?php

namespace App\Features\Course\Http\Resources;

use App\Features\Lesson\Http\Resources\LessonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->course_name,
            'description' => $this->description,
            'minimum_score' => $this->minimum_score,
            'status' => $this->status,
            'total_lessons' => $this->whenCounted('lessons'),
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
        ];
    }
}
