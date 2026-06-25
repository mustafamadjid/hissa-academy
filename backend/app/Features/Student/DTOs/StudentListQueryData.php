<?php

namespace App\Features\Student\DTOs;

final readonly class StudentListQueryData
{
    public function __construct(
        public ?string $search,
        public ?bool $emailVerified,
        public string $sortBy,
        public string $sortDirection,
        public int $limit,
        public int $page,
    ) {}
}
