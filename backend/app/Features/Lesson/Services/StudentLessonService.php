<?php

namespace App\Features\Lesson\Services;

use App\Features\Course\Services\StudentCourseService;
use App\Features\Lesson\Contracts\LessonRepositoryContract;
use App\Features\Lesson\Exceptions\LessonOperationException;
use App\Features\User\Models\User;
use App\Features\UserProgress\Contracts\UserProgressRepositoryContract;
use App\GlobalExceptions\AuthorizationException;
use App\Helper\EnsureStudentForService;
use Throwable;

final class StudentLessonService
{
    public function __construct(
        private readonly LessonRepositoryContract $lessonRepository,
        private readonly UserProgressRepositoryContract $progressRepository,
        private readonly StudentCourseService $studentCourseService,
        private readonly EnsureStudentForService $ensureStudent,
    ) {}

    /**
     * @return array<string, mixed>|null
     */
    public function detail(string $lessonId, ?User $actor): ?array
    {
        $this->ensureStudent->ensureStudent($actor);

        try {
            $lesson = $this->lessonRepository->findActiveCourseLessonById($lessonId);

            if ($lesson === null) {
                return null;
            }

            $lessons = $lesson->course->lessons;
            $progressByLesson = $this->progressRepository->forUserLessons($actor->id, $lessons->pluck('id')->all());
            $completedRequiredLessonIds = $progressByLesson
                ->filter(fn ($progress): bool => $progress->status === 'completed')
                ->keys()
                ->all();
            $isLocked = $this->studentCourseService->isLessonLocked($lesson, $lessons, $completedRequiredLessonIds);

            if ($isLocked) {
                throw new AuthorizationException('Lesson masih terkunci.');
            }

            return [
                'id' => $lesson->id,
                'course_id' => $lesson->course_id,
                'title' => $lesson->title,
                'position' => $lesson->position,
                'is_required' => $lesson->is_required,
                'is_locked' => false,
                'video' => $lesson->video,
                'progress' => $progressByLesson->get($lesson->id),
            ];
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new LessonOperationException('Gagal mengambil detail lesson.', $exception);
        }
    }
}
