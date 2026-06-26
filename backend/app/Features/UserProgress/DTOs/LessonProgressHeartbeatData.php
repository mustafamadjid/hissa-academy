<?php

namespace App\Features\UserProgress\DTOs;

final readonly class LessonProgressHeartbeatData
{
    public function __construct(
        public int $lastPositionSeconds,
        public int $watchedSeconds,
    ) {}
}
