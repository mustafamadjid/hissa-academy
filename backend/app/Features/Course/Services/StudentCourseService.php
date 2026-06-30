<?php

namespace App\Features\Course\Services;

use App\Features\Course\Contracts\CourseRepositoryContract;
use App\Features\Course\Exceptions\CourseOperationException;
use App\Features\Course\Models\Course;
use App\Features\User\Models\User;
use App\Features\UserProgress\Contracts\UserProgressRepositoryContract;
use App\Helper\EnsureStudentForService;
use Illuminate\Support\Facades\Log;
use Throwable;

final class StudentCourseService
{
    public function __construct(
        private readonly CourseRepositoryContract $courseRepository,
        private readonly UserProgressRepositoryContract $progressRepository,
        private readonly EnsureStudentForService $ensureStudent,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listAvailable(?User $actor): array
    {
        $this->ensureStudent->ensureStudent($actor);

        try {
            return $this->courseRepository->activeCoursesWithLessons()
                ->map(fn (Course $course): array => $this->courseSummary($course, $actor))
                ->values()
                ->all();
        } catch (Throwable $exception) {
            Log::error('Gagal mengambil daftar course.', [
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new CourseOperationException('Gagal mengambil daftar course.', $exception);
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function detail(string $courseId, ?User $actor): ?array
    {
        $this->ensureStudent->ensureStudent($actor);

        try {
            $course = $this->courseRepository->findActiveWithLessons($courseId);

            if ($course === null) {
                return null;
            }

            $lessons = $course->lessons;
            $progressByLesson = $this->progressByLesson($actor->id, $lessons->pluck('id')->all());
            $completedRequiredLessonIds = $progressByLesson
                ->filter(fn ($progress): bool => $progress->status === 'completed')
                ->keys()
                ->all();

            $lessonData = $lessons
                ->map(fn ($lesson): array => [
                    'id' => $lesson->id,
                    'course_id' => $lesson->course_id,
                    'title' => $lesson->title,
                    'position' => $lesson->position,
                    'is_required' => $lesson->is_required,
                    'is_locked' => $this->isLessonLocked($lesson, $lessons, $completedRequiredLessonIds),
                    'video' => $lesson->video,
                    'progress' => $progressByLesson->get($lesson->id),
                ])
                ->values()
                ->all();

            return array_merge($this->courseSummary($course, $actor), [
                'lessons' => $lessonData,
            ]);
        } catch (Throwable $exception) {
            Log::error('Gagal mengambil detail course.', [
                'course_id' => $courseId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new CourseOperationException('Gagal mengambil detail course.', $exception);
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function progress(string $courseId, ?User $actor): ?array
    {
        $this->ensureStudent->ensureStudent($actor);

        try {
            $course = $this->courseRepository->findActiveWithLessons($courseId);

            return $course === null ? null : $this->courseProgress($course, $actor);
        } catch (Throwable $exception) {
            Log::error('Gagal mengambil progress course.', [
                'course_id' => $courseId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new CourseOperationException('Gagal mengambil progress course.', $exception);
        }
    }

    /**
     * @param  iterable<int, mixed>  $lessons
     * @param  array<int, string>  $completedRequiredLessonIds
     */
    public function isLessonLocked(mixed $lesson, iterable $lessons, array $completedRequiredLessonIds): bool
    {
        foreach ($lessons as $courseLesson) {
            if ($courseLesson->id === $lesson->id) {
                return false;
            }

            if ($courseLesson->is_required && ! in_array($courseLesson->id, $completedRequiredLessonIds, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    private function courseSummary(Course $course, User $actor): array
    {
        return array_merge([
            'id' => $course->id,
            'name' => $course->course_name,
            'description' => $course->description,
            'minimum_score' => $course->minimum_score,
            'status' => $course->status,
        ], $this->courseProgress($course, $actor));
    }

    /**
     * @return array<string, mixed>
     */
    private function courseProgress(Course $course, User $actor): array
    {
        $lessonIds = $course->lessons->pluck('id')->all();
        $progressByLesson = $this->progressByLesson($actor->id, $lessonIds);
        $totalLessons = count($lessonIds);
        $completedLessons = $progressByLesson
            ->filter(fn ($progress): bool => $progress->status === 'completed')
            ->count();

        return [
            'course_id' => $course->id,
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'progress_percentage' => $totalLessons === 0 ? 0 : (int) round(($completedLessons / $totalLessons) * 100),
        ];
    }

    private function progressByLesson(string $userId, array $lessonIds)
    {
        if ($lessonIds === []) {
            return collect();
        }

        return $this->progressRepository->forUserLessons($userId, $lessonIds);
    }
}
