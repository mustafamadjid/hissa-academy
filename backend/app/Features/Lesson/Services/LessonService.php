<?php

namespace App\Features\Lesson\Services;

use App\Features\Lesson\Contracts\LessonRepositoryContract;
use App\Features\Lesson\DTOs\LessonCreateData;
use App\Features\Lesson\DTOs\LessonUpdateData;
use App\Features\Lesson\Exceptions\LessonOperationException;
use App\Features\Lesson\Models\Lesson;
use App\Features\User\Models\User;
use App\Helper\EnsureAdminForService;
use Illuminate\Support\Collection;
use App\Features\LessonVideo\Services\LessonVideoService;
use Illuminate\Support\Facades\Log;
use Throwable;

final class LessonService
{
    public function __construct(
        private readonly LessonRepositoryContract $lessonRepository,
        private readonly EnsureAdminForService $ensureAdmin,
        private readonly LessonVideoService $videoService
    ) {}

    public function listByCourse(string $courseId): ?Collection
    {
        try {
            $course = $this->lessonRepository->findCourseById($courseId);

            if ($course === null) {
                return null;
            }

            return $this->lessonRepository->listByCourse($course);
        } catch (Throwable $exception) {
            Log::error('Gagal mengambil daftar lesson.', [
                'course_id' => $courseId,
                'exception' => $exception,
            ]);

            throw new LessonOperationException('Gagal mengambil daftar lesson.', $exception);
        }
    }

    public function create(string $courseId, LessonCreateData $data, ?User $actor): ?Lesson
    {
        $this->ensureAdmin->ensureAdmin($actor);

        try {
            $course = $this->lessonRepository->findCourseById($courseId);

            if ($course === null) {
                return null;
            }

            $metadata = $this->videoService->getVideoMetadata($data->youtubeVideoId);

            return $this->lessonRepository->create($course, $data, $metadata);
        } catch (Throwable $exception) {
            Log::error('Gagal membuat lesson.', [
                'course_id' => $courseId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new LessonOperationException('Gagal membuat lesson.', $exception);
        }
    }

    public function findById(string $lessonId): ?Lesson
    {
        try {
            return $this->lessonRepository->findById($lessonId);
        } catch (Throwable $exception) {
            Log::error('Gagal mengambil detail lesson.', [
                'lesson_id' => $lessonId,
                'exception' => $exception,
            ]);

            throw new LessonOperationException('Gagal mengambil detail lesson.', $exception);
        }
    }

    public function update(string $lessonId, LessonUpdateData $data, ?User $actor): ?Lesson
    {
        $this->ensureAdmin->ensureAdmin($actor);

        try {
            $lesson = $this->lessonRepository->findById($lessonId);

            if ($lesson === null) {
                return null;
            }

            return $this->lessonRepository->update($lesson, $data);
        } catch (Throwable $exception) {
            Log::error('Gagal memperbarui lesson.', [
                'lesson_id' => $lessonId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new LessonOperationException('Gagal memperbarui lesson.', $exception);
        }
    }

    public function delete(string $lessonId, ?User $actor): ?bool
    {
        $this->ensureAdmin->ensureAdmin($actor);
        try {
            $lesson = $this->lessonRepository->findById($lessonId);

            if ($lesson === null) {
                return null;
            }

            return $this->lessonRepository->delete($lesson);
        } catch (Throwable $exception) {
            Log::error('Gagal menghapus lesson.', [
                'lesson_id' => $lessonId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new LessonOperationException('Gagal menghapus lesson.', $exception);
        }
    }

    public function reorder(string $courseId, array $lessons, ?User $actor): ?Lesson
    {
        $this->ensureAdmin->ensureAdmin($actor);

        try {
            $course = $this->lessonRepository->findCourseById($courseId);

            if ($course === null) {
                return null;
            }

            return $this->lessonRepository->reorder($courseId, $lessons);
        } catch (Throwable $exception) {
            Log::error('Gagal mengurutkan lesson.', [
                'course_id' => $courseId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new LessonOperationException('Gagal mengurutkan lesson.', $exception);
        }
    }

}
