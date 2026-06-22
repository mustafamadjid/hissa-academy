<?php

namespace App\Features\Course\DTOs;

final readonly class CourseData
{
    public function __construct(
        public readonly string $courseName,
        public readonly string $description,
        public readonly float $minimumScore,
        public readonly string $status
    ) {}

    public function toArray(): array
    {
        return [
            'course_name' => $this->courseName,
            'description' => $this->description,
            'minimum_score' => $this->minimumScore,
            'status' => $this->status,
        ];
    }
}
