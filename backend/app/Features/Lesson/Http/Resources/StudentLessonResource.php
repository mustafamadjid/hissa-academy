<?php

namespace App\Features\Lesson\Http\Resources;

use App\Features\UserProgress\Http\Resources\UserProgressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class StudentLessonResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lesson = $this->resource;
        $video = $lesson['video'] ?? null;

        return [
            'id' => $lesson['id'],
            'course_id' => $lesson['course_id'],
            'title' => $lesson['title'],
            'position' => $lesson['position'],
            'is_required' => $lesson['is_required'],
            'is_locked' => $lesson['is_locked'],
            'video' => $video === null ? null : [
                'id' => $video->id,
                'youtube_video_id' => $video->video_id ?? $this->extractYoutubeVideoId($video->video_url),
                'video_url' => $video->video_url,
                'title' => $video->title,
                'description' => $video->description,
                'channel_title' => $video->channel_title,
                'thumbnail_url' => $video->thumbnail_url,
                'duration_iso' => $video->duration_iso,
                'duration_seconds' => $video->duration_seconds,
                'privacy_status' => $video->privacy_status,
            ],
            'progress' => new UserProgressResource($lesson['progress']),
        ];
    }

    private function extractYoutubeVideoId(string $videoUrl): ?string
    {
        $query = [];
        parse_str((string) parse_url($videoUrl, PHP_URL_QUERY), $query);

        if (isset($query['v']) && is_string($query['v'])) {
            return $query['v'];
        }

        $path = trim((string) parse_url($videoUrl, PHP_URL_PATH), '/');

        return $path !== '' ? basename($path) : null;
    }
}
