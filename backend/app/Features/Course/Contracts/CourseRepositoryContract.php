<?php

namespace App\Features\Course\Contracts;

use App\Features\Course\DTOs\CourseListQueryData;
use App\Features\Course\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CourseRepositoryContract
{
    public function all(CourseListQueryData $query): LengthAwarePaginator;

    /**
     * @return Collection<int, Course>
     */
    public function activeCoursesWithLessons(): Collection;

    public function findActiveWithLessons(string $id): ?Course;

    public function findWithLessonsById(string $id): ?Course;

    public function findById(string $id): ?Course;

    public function create(array $data): Course;

    public function update(string $id, array $data): ?Course;

    public function delete(string $id): bool;
}
