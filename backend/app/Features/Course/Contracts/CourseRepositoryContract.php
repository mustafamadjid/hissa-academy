<?php

namespace App\Features\Course\Contracts;

use App\Features\Course\DTOs\CourseListQueryData;
use App\Features\Course\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CourseRepositoryContract
{
    public function all(CourseListQueryData $query): LengthAwarePaginator;

    public function findById(string $id): ?Course;

    public function create(array $data): Course;

    public function update(string $id, array $data): ?Course;

    public function delete(string $id): bool;
}
