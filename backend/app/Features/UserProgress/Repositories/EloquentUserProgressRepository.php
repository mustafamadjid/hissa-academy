<?php

namespace App\Features\UserProgress\Repositories;

use App\Features\UserProgress\Contracts\UserProgressRepositoryContract;
use App\Features\UserProgress\Models\UserProgress;
use Illuminate\Support\Collection;

final class EloquentUserProgressRepository implements UserProgressRepositoryContract
{
    public function forUserLessons(string $userId, array $lessonIds): Collection
    {
        return UserProgress::query()
            ->where('user_id', $userId)
            ->whereIn('lesson_id', $lessonIds)
            ->get()
            ->keyBy('lesson_id');
    }


    public function findForUserLesson(string $userId, string $lessonId): ?UserProgress
    {
        return UserProgress::query()
            ->where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->first();
    }

    public function saveForUserLesson(string $userId, string $lessonId, array $attributes): UserProgress
    {
        $progress = $this->findForUserLesson($userId, $lessonId);

        if ($progress === null) {
            return UserProgress::query()->create(array_merge([
                'user_id' => $userId,
                'lesson_id' => $lessonId,
            ], $attributes));
        }

        $progress->update($attributes);

        return $progress->refresh();
    }
}
