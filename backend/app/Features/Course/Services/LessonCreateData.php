<?php

namespace App\Features\Lesson\DTOs;

final readonly class LessonCreateData
{
    public function __construct(
        public string $title,
        public string $youtubeVideoId,
        public int $position,
        public bool $isRequired,
        public int $durationSeconds = 0,
    ) {}

    public function youtubeVideoUrl(): string
    {
        return "https://www.youtube.com/watch?v={$this->youtubeVideoId}";
    }
}
