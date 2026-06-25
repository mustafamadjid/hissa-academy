<?php

namespace App\Features\Student\Repositories;

use App\Features\Certificate\Models\Certificate;
use App\Features\Course\Models\Course;
use App\Features\Student\Contracts\StudentRepositoryContract;
use App\Features\Student\DTOs\StudentListQueryData;
use App\Features\Student\DTOs\StudentPaginationData;
use App\Features\User\Enums\UserRole;
use App\Features\User\Models\User;
use App\Features\UserProgress\Models\UserProgress;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class EloquentStudentRepository implements StudentRepositoryContract
{
    public function all(StudentListQueryData $query): LengthAwarePaginator
    {
        $search = trim((string) $query->search);
        $emailVerified = $query->emailVerified;

        return User::query()
            ->with('role')
            ->whereHas('role', fn ($query) => $query->where('name', UserRole::STUDENT->value))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($emailVerified !== null, function ($query) use ($emailVerified): void {
                $query->when(
                    $emailVerified,
                    fn ($query) => $query->whereNotNull('email_verified_at'),
                    fn ($query) => $query->whereNull('email_verified_at'),
                );
            })
            ->orderBy($query->sortBy, $query->sortDirection)
            ->paginate(
                $query->limit,
                [
                    'id',
                    'role_id',
                    'email',
                    'phone',
                    'full_name',
                    'birth_date',
                    'avatar_url',
                    'last_login_at',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
                'page',
                $query->page
            );
    }

    public function findStudentById(string $studentId): ?User
    {
        return User::query()
            ->with('role')
            ->whereKey($studentId)
            ->whereHas('role', fn ($query) => $query->where('name', UserRole::STUDENT->value))
            ->first();
    }

    public function learningSummary(string $studentId): array
    {
        $progress = UserProgress::query()
            ->where('user_id', $studentId);

        return [
            'total_progress_records' => (clone $progress)->count(),
            'completed_lessons' => (clone $progress)->where('status', 'completed')->count(),
            'in_progress_lessons' => (clone $progress)->where('status', 'in_progress')->count(),
            'total_watched_seconds' => (int) (clone $progress)->sum('total_watched_seconds'),
            'total_certificates' => Certificate::query()
                ->where('user_id', $studentId)
                ->count(),
        ];
    }

    public function progressByCourse(string $studentId): Collection
    {
        $progressByLesson = UserProgress::query()
            ->where('user_id', $studentId)
            ->get()
            ->keyBy('lesson_id');

        return Course::query()
            ->with(['lessons' => fn ($query) => $query->orderBy('position')])
            ->orderBy('course_name')
            ->get()
            ->map(function (Course $course) use ($progressByLesson): array {
                $lessons = $course->lessons->map(function ($lesson) use ($progressByLesson): array {
                    $progress = $progressByLesson->get($lesson->id);

                    return [
                        'id' => $lesson->id,
                        'title' => $lesson->title,
                        'position' => $lesson->position,
                        'is_required' => $lesson->is_required,
                        'progress' => [
                            'status' => $progress?->status ?? 'not_started',
                            'last_position_seconds' => $progress?->last_position_seconds ?? 0,
                            'total_watched_seconds' => $progress?->total_watched_seconds ?? 0,
                            'completed_at' => $progress?->completed_at?->toISOString(),
                        ],
                    ];
                });

                $totalLessons = $lessons->count();
                $completedLessons = $lessons
                    ->where('progress.status', 'completed')
                    ->count();

                return [
                    'course' => [
                        'id' => $course->id,
                        'name' => $course->course_name,
                        'status' => $course->status,
                    ],
                    'summary' => [
                        'total_lessons' => $totalLessons,
                        'completed_lessons' => $completedLessons,
                        'progress_percentage' => $totalLessons === 0
                            ? 0
                            : (int) round(($completedLessons / $totalLessons) * 100),
                    ],
                    'lessons' => $lessons->values()->all(),
                ];
            });
    }

    public function certificates(string $studentId, StudentPaginationData $pagination): LengthAwarePaginator
    {
        return Certificate::query()
            ->with('course')
            ->where('user_id', $studentId)
            ->orderByDesc('issued_at')
            ->paginate(
                $pagination->limit,
                [
                    'id',
                    'user_id',
                    'course_id',
                    'certificate_number',
                    'issued_at',
                    'status',
                    'pdf_path',
                ],
                'page',
                $pagination->page
            );
    }
}
