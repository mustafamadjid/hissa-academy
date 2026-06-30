<?php

namespace App\Features\UserProgress\Services;

use App\Features\Lesson\Contracts\LessonRepositoryContract;
use App\Features\User\Models\User;
use App\Features\UserProgress\Contracts\UserProgressRepositoryContract;
use App\Features\UserProgress\DTOs\LessonProgressHeartbeatData;
use App\Features\UserProgress\Exceptions\UserProgressOperationException;
use App\Features\UserProgress\Models\UserProgress;
use App\GlobalExceptions\AuthorizationException;
use App\Helper\EnsureStudentForService;
use Illuminate\Support\Facades\Log;
use Throwable;

final class StudentLessonProgressService
{
    public function __construct(
        private readonly LessonRepositoryContract $lessonRepository,
        private readonly UserProgressRepositoryContract $progressRepository,
        private readonly EnsureStudentForService $ensureStudent,
    ) {}

    public function get(string $lessonId, ?User $actor): ?UserProgress
    {
        $this->ensureStudent->ensureStudent($actor);

        try {
            $lesson = $this->lessonRepository->findActiveCourseLessonById($lessonId);

            if ($lesson === null) {
                return null;
            }

            return $this->progressRepository->findForUserLesson($actor->id, $lesson->id)
                ?? new UserProgress([
                    'lesson_id' => $lesson->id,
                    'last_position_seconds' => 0,
                    'total_watched_seconds' => 0,
                    'status' => 'not_started',
                    'completed_at' => null,
                ]);
        } catch (Throwable $exception) {
            Log::error('Gagal mengambil progress lesson.', [
                'lesson_id' => $lessonId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new UserProgressOperationException('Gagal mengambil progress lesson.', $exception);
        }
    }

    public function heartbeat(string $lessonId, LessonProgressHeartbeatData $data, ?User $actor): ?UserProgress
    {
        $this->ensureStudent->ensureStudent($actor);

        try {
            $lesson = $this->lessonRepository->findActiveCourseLessonById($lessonId);

            if ($lesson === null) {
                return null;
            }

            if ($this->isLocked($lesson, $actor)) {
                throw new AuthorizationException('Lesson masih terkunci.');
            }

            $existing = $this->progressRepository->findForUserLesson($actor->id, $lesson->id);
            $totalWatchedSeconds = ($existing?->total_watched_seconds ?? 0) + $data->watchedSeconds;
            $status = $this->resolveStatus($totalWatchedSeconds, (int) ($lesson->video?->duration_seconds ?? 0));
            $completedAt = $status === 'completed'
                ? ($existing?->completed_at ?? now())
                : null;

            return $this->progressRepository->saveForUserLesson($actor->id, $lesson->id, [
                'last_position_seconds' => $data->lastPositionSeconds,
                'total_watched_seconds' => $totalWatchedSeconds,
                'status' => $status,
                'completed_at' => $completedAt,
            ]);
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Gagal menyimpan progress lesson.', [
                'lesson_id' => $lessonId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new UserProgressOperationException('Gagal menyimpan progress lesson.', $exception);
        }
    }

    private function resolveStatus(int $totalWatchedSeconds, int $durationSeconds): string
    {
        if ($durationSeconds > 0 && $totalWatchedSeconds >= (int) ceil($durationSeconds * 0.9)) {
            return 'completed';
        }

        return $totalWatchedSeconds > 0 ? 'in_progress' : 'not_started';
    }

    private function isLocked(mixed $lesson, User $actor): bool
    {
        $lessons = $lesson->course->lessons;
        $progressByLesson = $this->progressRepository->forUserLessons($actor->id, $lessons->pluck('id')->all());
        $completedRequiredLessonIds = $progressByLesson
            ->filter(fn ($progress): bool => $progress->status === 'completed')
            ->keys()
            ->all();

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
}
