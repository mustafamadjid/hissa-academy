<?php

namespace App\Features\Student\Services;

use App\Features\Student\Contracts\StudentRepositoryContract;
use App\Features\Student\DTOs\StudentListQueryData;
use App\Features\Student\DTOs\StudentPaginationData;
use App\Features\Student\Exceptions\StudentOperationException;
use App\Features\User\Models\User;
use App\Helper\EnsureAdminForService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

final class StudentService
{
    public function __construct(
        private readonly StudentRepositoryContract $studentRepository,
        private readonly EnsureAdminForService $ensureAdmin,
    ) {}

    public function all(StudentListQueryData $query, ?User $actor): LengthAwarePaginator
    {
        $this->ensureAdmin->ensureAdmin($actor);

        try {
            return $this->studentRepository->all($query);
        } catch (Throwable $exception) {
            Log::error('Gagal mengambil daftar student.', [
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new StudentOperationException('Gagal mengambil daftar student.', $exception);
        }
    }

    public function detail(string $studentId, ?User $actor): ?array
    {
        $this->ensureAdmin->ensureAdmin($actor);

        try {
            $student = $this->studentRepository->findStudentById($studentId);

            if ($student === null) {
                return null;
            }

            return [
                'student' => $student,
                'summary' => $this->studentRepository->learningSummary($studentId),
            ];
        } catch (Throwable $exception) {
            Log::error('Gagal mengambil detail student.', [
                'student_id' => $studentId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new StudentOperationException('Gagal mengambil detail student.', $exception);
        }
    }

    public function progress(string $studentId, ?User $actor): ?Collection
    {
        $this->ensureAdmin->ensureAdmin($actor);

        try {
            if ($this->studentRepository->findStudentById($studentId) === null) {
                return null;
            }

            return $this->studentRepository->progressByCourse($studentId);
        } catch (Throwable $exception) {
            Log::error('Gagal mengambil progress student.', [
                'student_id' => $studentId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new StudentOperationException('Gagal mengambil progress student.', $exception);
        }
    }

    public function certificates(
        string $studentId,
        StudentPaginationData $pagination,
        ?User $actor,
    ): ?LengthAwarePaginator {
        $this->ensureAdmin->ensureAdmin($actor);

        try {
            if ($this->studentRepository->findStudentById($studentId) === null) {
                return null;
            }

            return $this->studentRepository->certificates($studentId, $pagination);
        } catch (Throwable $exception) {
            Log::error('Gagal mengambil sertifikat student.', [
                'student_id' => $studentId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new StudentOperationException('Gagal mengambil sertifikat student.', $exception);
        }
    }
}
