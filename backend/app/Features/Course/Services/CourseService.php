<?php

namespace App\Features\Course\Services;

use App\Features\Course\Contracts\CourseRepositoryContract;
use App\Features\Course\DTOs\CourseListQueryData;
use App\Features\Course\Exceptions\CourseOperationException;
use App\Features\Course\Models\Course;
use App\Features\User\Enums\UserRole;
use App\Features\User\Models\User;
use App\GlobalExceptions\AuthorizationException;
use App\GlobalExceptions\AuthorizatrionException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Throwable;

final class CourseService
{
    public function __construct(
        private readonly CourseRepositoryContract $courseRepository,
    ) {}

    public function all(CourseListQueryData $query): LengthAwarePaginator
    {
        try {
            return $this->courseRepository->all($query);
        } catch (Throwable $exception) {
            throw new CourseOperationException('Gagal mengambil daftar course.', $exception);
        }
    }

    public function findById(string $id): ?Course
    {
        try {
            return $this->courseRepository->findById($id);
        } catch (Throwable $exception) {
            throw new CourseOperationException('Gagal mengambil detail course.', $exception);
        }
    }

    public function create(array $data, ?User $actor): Course
    {
        $this->ensureAdmin($actor);

        try {
            return $this->courseRepository->create($data);
        } catch (Throwable $exception) {
            throw new CourseOperationException('Gagal membuat course.', $exception);
        }
    }

    public function update(string $id, array $data, ?User $actor): ?Course
    {
        $this->ensureAdmin($actor);

        try {
            return $this->courseRepository->update($id, $data);
        } catch (Throwable $exception) {
            throw new CourseOperationException('Gagal memperbarui course.', $exception);
        }
    }

    public function delete(string $id, ?User $actor): bool
    {
        $this->ensureAdmin($actor);

        try {
            return $this->courseRepository->delete($id);
        } catch (Throwable $exception) {
            throw new CourseOperationException('Gagal menghapus course.', $exception);
        }
    }

    private function ensureAdmin(?User $actor): void
    {
        if ($actor === null || $actor->role?->name !== UserRole::ADMIN->value) {
            throw new AuthorizationException();
        }
    }
}
