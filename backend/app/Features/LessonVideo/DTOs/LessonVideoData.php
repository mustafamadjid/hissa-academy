<?php
namespace App\Features\LessonVideo\DTOs;

final readonly class LessonVideoData
{
    public function __construct(
        public readonly string $videoId,
        public readonly string $title,
        public readonly string $description,
        public readonly string $channelTitle,
        public readonly string $thumbnailUrl,
        public readonly string $durationIso,
        public readonly int $durationSeconds,
        public readonly string $privacyStatus,
    ) {}
}

?>