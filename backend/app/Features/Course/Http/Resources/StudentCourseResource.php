<?php

namespace App\Features\Course\Http\Resources;

use App\Features\Lesson\Http\Resources\StudentLessonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class StudentCourseResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $course = $this->resource;

        return [
            'id' => $course['id'],
            'name' => $course['name'],
            'description' => $course['description'],
            'minimum_score' => $course['minimum_score'],
            'status' => $course['status'],
            'total_lessons' => $course['total_lessons'],
            'completed_lessons' => $course['completed_lessons'],
            'progress_percentage' => $course['progress_percentage'],
            'lessons' => $this->when(
                array_key_exists('lessons', $course),
                fn () => StudentLessonResource::collection(collect($course['lessons'])),
            ),
        ];
    }
}
