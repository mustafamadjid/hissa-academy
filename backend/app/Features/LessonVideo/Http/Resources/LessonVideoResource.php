<?php

namespace App\Features\LessonVideo\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class LessonVideoResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lesson_id' => $this->lesson_id,
            'youtube_video_id' => $this->video_id,
            'video_url' => $this->video_url,
            'title' => $this->title,
            'description' => $this->description,
            'channel_title' => $this->channel_title,
            'thumbnail_url' => $this->thumbnail_url,
            'duration_iso' => $this->duration_iso,
            'duration_seconds' => $this->duration_seconds,
            'privacy_status' => $this->privacy_status,
        ];
    }
}
