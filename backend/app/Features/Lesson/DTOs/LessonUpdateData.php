<?php

namespace App\Features\Lesson\DTOs;

final readonly class LessonUpdateData
{
    public function __construct(
        public ?string $title = null,
        public ?int $position = null,
        public ?bool $isRequired = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function lessonAttributes(): array
    {
        return array_filter([
            'title' => $this->title,
            'is_required' => $this->isRequired,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
