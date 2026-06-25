<?php

namespace App\Features\Student\Contracts;

use App\Features\Student\DTOs\StudentListQueryData;
use App\Features\Student\DTOs\StudentPaginationData;
use App\Features\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface StudentRepositoryContract
{
    public function all(StudentListQueryData $query): LengthAwarePaginator;

    public function findStudentById(string $studentId): ?User;

    /**
     * @return array<string, int>
     */
    public function learningSummary(string $studentId): array;

    public function progressByCourse(string $studentId): Collection;

    public function certificates(string $studentId, StudentPaginationData $pagination): LengthAwarePaginator;
}
