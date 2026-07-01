<?php

namespace App\Features\Course\DTOs;

final readonly class StudentCourseListData
{
    public function __construct(
        public ?string $search,
        public int $perPage,
    ) {}
}
