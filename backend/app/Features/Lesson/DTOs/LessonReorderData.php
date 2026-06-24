<?php
namespace App\Features\Lesson\DTOs;

final readonly class LessonReorderData
{
    public function __construct(
        public readonly array $lessonIds,
    ) {}
}

?>