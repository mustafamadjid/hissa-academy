<?php

namespace App\Features\Course\Repositories;

use App\Features\Course\Contracts\CourseRepositoryContract;
use App\Features\Course\DTOs\CourseListQueryData;
use App\Features\Course\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class EloquentCourseRepository implements CourseRepositoryContract
{
    public function all(CourseListQueryData $query): LengthAwarePaginator
    {
        $search = trim((string) $query->search);
        $sortBy = $query->sortBy;
        $sortDirection = $query->sortDirection;
        $limit = $query->limit;
        $page = $query->page;

        return Course::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('course_name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy($sortBy, $sortDirection)
            ->paginate($limit, ['id', 'course_name', 'description', 'minimum_score', 'status'], 'page', $page);
    }

    public function activeCoursesWithLessons(): Collection
    {
        return Course::query()
            ->with(['lessons' => fn ($query) => $query->orderBy('position')])
            ->where('status', 'active')
            ->orderBy('course_name')
            ->get();
    }

    public function findActiveWithLessons(string $id): ?Course
    {
        return Course::query()
            ->with(['lessons' => fn ($query) => $query->with('video')->orderBy('position')])
            ->where('status', 'active')
            ->find($id);
    }

    public function findById(string $id): ?Course
    {
        return Course::find($id);
    }

    public function create(array $data): Course
    {
        return Course::create($data);
    }

    public function update(string $id, array $data): ?Course
    {
        $course = $this->findById($id);

        if ($course === null) {
            return null;
        }

        $course->update($data);

        return $course->refresh();
    }

    public function delete(string $id): bool
    {
        $course = $this->findById($id);

        if ($course === null) {
            return false;
        }

        return (bool) $course->delete();
    }
}
