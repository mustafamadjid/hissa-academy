<?php

namespace App\Features\Course\Http\Resources;

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
        ];
    }
}
