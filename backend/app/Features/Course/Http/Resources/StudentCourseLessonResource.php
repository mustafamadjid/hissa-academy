<?php

namespace App\Features\Course\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class StudentCourseLessonResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lesson = $this->resource;

        return [
            'id' => $lesson['id'],
            'course_id' => $lesson['course_id'],
            'title' => $lesson['title'],
            'position' => $lesson['position'],
            'is_required' => $lesson['is_required'],
            'is_locked' => $lesson['is_locked'],
        ];
    }
}
