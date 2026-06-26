<?php

namespace App\Features\UserProgress\Contracts;

use App\Features\UserProgress\Models\UserProgress;
use Illuminate\Support\Collection;

interface UserProgressRepositoryContract
{
    /**
     * @param  array<int, string>  $lessonIds
     * @return Collection<string, UserProgress>
     */
    public function forUserLessons(string $userId, array $lessonIds): Collection;

    public function findForUserLesson(string $userId, string $lessonId): ?UserProgress;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function saveForUserLesson(string $userId, string $lessonId, array $attributes): UserProgress;
}
