<?php

namespace App\Features\Course\DTOs;

final readonly class CourseListQueryData
{
    public function __construct(
        public ?string $search = '',
        public string $sortBy = 'created_at',
        public string $sortDirection = 'desc',
        public int $limit = 10,
        public int $page = 1,
    ) {}

    /**
     * @return array<string, string|int|null>
     */
    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'sort_by' => $this->sortBy,
            'sort_direction' => $this->sortDirection,
            'limit' => $this->limit,
            'page' => $this->page,
        ];
    }
}
