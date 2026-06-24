<?php

namespace App\Features\Lesson\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'title' => $this->title,
            'position' => $this->position,
            'is_required' => $this->is_required,
            'video' => $this->whenLoaded('video', function (): ?array {
                if ($this->video === null) {
                    return null;
                }

                return [
                    'id' => $this->video->id,
                    'youtube_video_id' => $this->video->video_id ?? $this->extractYoutubeVideoId($this->video->video_url),
                    'video_url' => $this->video->video_url,
                    'title' => $this->video->title,
                    'description' => $this->video->description,
                    'channel_title' => $this->video->channel_title,
                    'thumbnail_url' => $this->video->thumbnail_url,
                    'duration_iso' => $this->video->duration_iso,
                    'duration_seconds' => $this->video->duration_seconds,
                    'privacy_status' => $this->video->privacy_status,
                ];
            }),
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
