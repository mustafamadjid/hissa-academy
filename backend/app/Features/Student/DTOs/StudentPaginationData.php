<?php

namespace App\Features\Student\DTOs;

final readonly class StudentPaginationData
{
    public function __construct(
        public int $limit,
        public int $page,
    ) {}
}
