<?php

namespace App\Features\Lesson\Services;

use App\Features\Lesson\Contracts\LessonRepositoryContract;
use App\Features\Lesson\DTOs\LessonCreateData;
use App\Features\Lesson\DTOs\LessonUpdateData;
use App\Features\Lesson\Exceptions\LessonOperationException;
use App\Features\Lesson\Models\Lesson;
use App\Features\User\Enums\UserRole;
use App\Features\User\Models\User;
use App\GlobalExceptions\AuthorizationException;
use Illuminate\Support\Collection;
use Throwable;

final class LessonService
{
    public function __construct(
        private readonly LessonRepositoryContract $lessonRepository,
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
            throw new LessonOperationException('Gagal mengambil daftar lesson.', $exception);
        }
    }

    public function create(string $courseId, LessonCreateData $data, ?User $actor): ?Lesson
    {
        $this->ensureAdmin($actor);

        try {
            $course = $this->lessonRepository->findCourseById($courseId);

            if ($course === null) {
                return null;
            }

            return $this->lessonRepository->create($course, $data);
        } catch (Throwable $exception) {
            throw new LessonOperationException('Gagal membuat lesson.', $exception);
        }
    }

    public function findById(string $lessonId): ?Lesson
    {
        try {
            return $this->lessonRepository->findById($lessonId);
        } catch (Throwable $exception) {
            throw new LessonOperationException('Gagal mengambil detail lesson.', $exception);
        }
    }

    public function update(string $lessonId, LessonUpdateData $data, ?User $actor): ?Lesson
    {
        $this->ensureAdmin($actor);

        try {
            $lesson = $this->lessonRepository->findById($lessonId);

            if ($lesson === null) {
                return null;
            }

            return $this->lessonRepository->update($lesson, $data);
        } catch (Throwable $exception) {
            throw new LessonOperationException('Gagal memperbarui lesson.', $exception);
        }
    }

    public function delete(string $lessonId, ?User $actor): ?bool
    {
        $this->ensureAdmin($actor);

        try {
            $lesson = $this->lessonRepository->findById($lessonId);

            if ($lesson === null) {
                return null;
            }

            return $this->lessonRepository->delete($lesson);
        } catch (Throwable $exception) {
            throw new LessonOperationException('Gagal menghapus lesson.', $exception);
        }
    }

    private function ensureAdmin(?User $actor): void
    {
        if ($actor === null || $actor->role?->name !== UserRole::ADMIN->value) {
            throw new AuthorizationException();
        }
    }
}
