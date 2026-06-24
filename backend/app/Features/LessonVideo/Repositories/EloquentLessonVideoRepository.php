<?php

namespace App\Features\LessonVideo\Repositories;

use App\Features\Lesson\Models\Lesson;
use App\Features\LessonVideo\Contracts\LessonVideoRepositoryContract;
use App\Features\LessonVideo\DTOs\LessonVideoData;
use App\Features\LessonVideo\Models\LessonVideo;

final class EloquentLessonVideoRepository implements LessonVideoRepositoryContract
{
    public function findLessonById(string $lessonId): ?Lesson
    {
        return Lesson::query()
            ->with(['course', 'video'])
            ->find($lessonId);
    }

    public function saveMetadata(Lesson $lesson, LessonVideoData $data): LessonVideo
    {
        $attributes = [
            'video_url' => $this->youtubeVideoUrl($data->videoId),
            'video_id' => $data->videoId,
            'title' => $data->title,
            'description' => $data->description,
            'channel_title' => $data->channelTitle,
            'thumbnail_url' => $data->thumbnailUrl,
            'duration_iso' => $data->durationIso,
            'duration_seconds' => $data->durationSeconds,
            'privacy_status' => $data->privacyStatus,
        ];

        $video = LessonVideo::query()
            ->withTrashed()
            ->firstOrNew(['lesson_id' => $lesson->id]);

        $video->fill($attributes);
        $video->deleted_at = null;
        $video->save();

        return $video->refresh();
    }

    public function deleteForLesson(Lesson $lesson): bool
    {
        $video = $lesson->video;

        if ($video === null) {
            return false;
        }

        return (bool) $video->delete();
    }

    private function youtubeVideoUrl(string $videoId): string
    {
        return "https://www.youtube.com/watch?v={$videoId}";
    }
}
