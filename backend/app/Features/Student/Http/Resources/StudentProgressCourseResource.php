<?php

namespace App\Features\Student\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class StudentProgressCourseResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'course' => $this->resource['course'],
            'summary' => $this->resource['summary'],
            'lessons' => $this->resource['lessons'],
        ];
    }
}
