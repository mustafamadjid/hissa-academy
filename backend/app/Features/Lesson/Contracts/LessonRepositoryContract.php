<?php

namespace App\Features\Lesson\Contracts;

use App\Features\Course\Models\Course;
use App\Features\Lesson\DTOs\LessonCreateData;
use App\Features\Lesson\DTOs\LessonUpdateData;
use App\Features\Lesson\Models\Lesson;
use Illuminate\Support\Collection;

interface LessonRepositoryContract
{
    public function findCourseById(string $courseId): ?Course;

    /**
     * @return Collection<int, Lesson>
     */
    public function listByCourse(Course $course): Collection;

    public function findById(string $lessonId): ?Lesson;

    public function create(Course $course, LessonCreateData $data): Lesson;

    public function update(Lesson $lesson, LessonUpdateData $data): Lesson;

    public function delete(Lesson $lesson): bool;
}
