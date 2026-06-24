<?php

namespace App\Features\LessonVideo\Contracts;

use App\Features\Lesson\Models\Lesson;
use App\Features\LessonVideo\DTOs\LessonVideoData;
use App\Features\LessonVideo\Models\LessonVideo;

interface LessonVideoRepositoryContract
{
    public function findLessonById(string $lessonId): ?Lesson;

    public function saveMetadata(Lesson $lesson, LessonVideoData $data): LessonVideo;

    public function deleteForLesson(Lesson $lesson): bool;
}
