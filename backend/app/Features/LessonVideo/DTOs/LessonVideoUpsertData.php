<?php

namespace App\Features\LessonVideo\DTOs;

final readonly class LessonVideoUpsertData
{
    public function __construct(
        public string $youtubeVideoId,
    ) {}
}
