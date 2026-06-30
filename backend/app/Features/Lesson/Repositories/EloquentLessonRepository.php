<?php

namespace App\Features\Lesson\Repositories;

use App\Features\Course\Models\Course;
use App\Features\Lesson\Contracts\LessonRepositoryContract;
use App\Features\Lesson\DTOs\LessonCreateData;
use App\Features\Lesson\DTOs\LessonUpdateData;
use App\Features\Lesson\Models\Lesson;
use App\Features\LessonVideo\DTOs\LessonVideoData;
use App\Features\LessonVideo\Models\LessonVideo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class EloquentLessonRepository implements LessonRepositoryContract
{
    public function findCourseById(string $courseId): ?Course
    {
        return Course::query()->find($courseId);
    }

    public function listByCourse(Course $course): Collection
    {
        return Lesson::query()
            ->with('video')
            ->where('course_id', $course->id)
            ->orderBy('position')
            ->get();
    }

    public function findById(string $lessonId): ?Lesson
    {
        return Lesson::query()
            ->with(['course', 'video'])
            ->find($lessonId);
    }

    public function findActiveCourseLessonById(string $lessonId): ?Lesson
    {
        return Lesson::query()
            ->with([
                'course.lessons' => fn ($query) => $query->orderBy('position'),
                'video',
            ])
            ->whereHas('course', fn ($query) => $query->where('status', 'active'))
            ->find($lessonId);
    }

    public function create(Course $course, LessonCreateData $data, LessonVideoData $videoMetadata): Lesson
    {
        $metadata = [
            'video_url' => $this->youtubeVideoUrl($videoMetadata->videoId),
            'video_id' => $videoMetadata->videoId,
            'title' => $videoMetadata->title,
            'description' => $videoMetadata->description,
            'channel_title' => $videoMetadata->channelTitle,
            'thumbnail_url' => $videoMetadata->thumbnailUrl,
            'duration_iso' => $videoMetadata->durationIso,
            'duration_seconds' => $videoMetadata->durationSeconds,
            'privacy_status' => $videoMetadata->privacyStatus,
        ];

        return DB::transaction(function () use ($course, $data, $metadata): Lesson {
                $position = $this->normalizeInsertPosition(
                    $course->id,
                    $data->position
                );

                $this->shiftPositionsUp($course->id, $position);

                $lesson = Lesson::query()->create([
                    'course_id' => $course->id,
                    'title' => $data->title,
                    'position' => $position,
                    'is_required' => $data->isRequired,
                ]);

                LessonVideo::query()->create([
                    'lesson_id' => $lesson->id,
                    ...$metadata,
                ]);

                return $lesson->load(['course', 'video']);
            });
    }

    public function update(Lesson $lesson, LessonUpdateData $data): Lesson
    {
        return DB::transaction(function () use ($lesson, $data): Lesson {
            if ($data->position !== null && $data->position !== $lesson->position) {
                $this->moveLessonToPosition($lesson, $data->position);
                $lesson->refresh();
            }

            $attributes = $data->lessonAttributes();

            if ($attributes !== []) {
                $lesson->update($attributes);
            }

            return $lesson->refresh()->load(['course', 'video']);
        });
    }

    public function delete(Lesson $lesson): bool
    {
        return (bool) $lesson->delete();
    }

    public function reorder(string $courseId, array $lessons): ?Lesson
    {
        return DB::transaction(function () use ($courseId, $lessons): ?Lesson {
            $positionsByLessonId = collect($lessons)
                ->mapWithKeys(fn (array $lesson): array => [
                    (string) $lesson['id'] => (int) $lesson['position'],
                ]);

            $lessonIds = $positionsByLessonId->keys();

            $existingLessons = Lesson::query()
                ->where('course_id', $courseId)
                ->whereIn('id', $lessonIds)
                ->lockForUpdate()
                ->get(['id', 'position']);

            if ($existingLessons->count() !== $lessonIds->count()) {
                return null;
            }

            foreach ($existingLessons as $lesson) {
                $lesson->forceFill(['position' => -1 * $lesson->position])->save();
            }

            foreach ($existingLessons as $lesson) {
                $lesson->forceFill([
                    'position' => $positionsByLessonId->get($lesson->id),
                ])->save();
            }

            return Lesson::query()
                ->with(['course', 'video'])
                ->where('course_id', $courseId)
                ->whereIn('id', $lessonIds)
                ->orderBy('position')
                ->first();
        });
    }

    private function normalizeInsertPosition(string $courseId, int $requestedPosition): int
    {
        $maxPosition = (int) Lesson::query()
            ->where('course_id', $courseId)
            ->max('position');

        return min($requestedPosition, $maxPosition + 1);
    }

    private function moveLessonToPosition(Lesson $lesson, int $requestedPosition): void
    {
        $oldPosition = $lesson->position;
        $newPosition = min($requestedPosition, $this->maxPosition($lesson->course_id));

        $lesson->forceFill(['position' => 0])->save();

        if ($newPosition < $oldPosition) {
            $this->shiftRangeUp($lesson->course_id, $newPosition, $oldPosition - 1);
        } else {
            $this->shiftRangeDown($lesson->course_id, $oldPosition + 1, $newPosition);
        }

        $lesson->forceFill(['position' => $newPosition])->save();
    }

    private function maxPosition(string $courseId): int
    {
        return (int) Lesson::query()
            ->where('course_id', $courseId)
            ->max('position');
    }

    private function shiftPositionsUp(string $courseId, int $fromPosition): void
    {
        $lessons = Lesson::query()
            ->where('course_id', $courseId)
            ->where('position', '>=', $fromPosition)
            ->orderBy('position')
            ->get(['id', 'position']);

        foreach ($lessons as $lesson) {
            $lesson->forceFill(['position' => -1 * $lesson->position])->save();
        }

        foreach ($lessons as $lesson) {
            $lesson->forceFill(['position' => abs($lesson->position) + 1])->save();
        }
    }

    private function shiftRangeUp(string $courseId, int $fromPosition, int $toPosition): void
    {
        $lessons = Lesson::query()
            ->where('course_id', $courseId)
            ->whereBetween('position', [$fromPosition, $toPosition])
            ->orderBy('position')
            ->get(['id', 'position']);

        foreach ($lessons as $lesson) {
            $lesson->forceFill(['position' => -1 * $lesson->position])->save();
        }

        foreach ($lessons as $lesson) {
            $lesson->forceFill(['position' => abs($lesson->position) + 1])->save();
        }
    }

    private function shiftRangeDown(string $courseId, int $fromPosition, int $toPosition): void
    {
        $lessons = Lesson::query()
            ->where('course_id', $courseId)
            ->whereBetween('position', [$fromPosition, $toPosition])
            ->orderBy('position')
            ->get(['id', 'position']);

        foreach ($lessons as $lesson) {
            $lesson->forceFill(['position' => -1 * $lesson->position])->save();
        }

        foreach ($lessons as $lesson) {
            $lesson->forceFill(['position' => abs($lesson->position) - 1])->save();
        }
    }
    private function youtubeVideoUrl(string $videoId): string
    {
        return "https://www.youtube.com/watch?v={$videoId}";
    }
}
