<?php

namespace App\Features\UserProgress\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserProgressResource extends JsonResource
{
    /**
     * @return array<string, mixed>|null
     */
    public function toArray(Request $request): ?array
    {
        if ($this->resource === null) {
            return [
                'last_position_seconds' => 0,
                'total_watched_seconds' => 0,
                'status' => 'not_started',
                'completed_at' => null,
            ];
        }

        return [
            'id' => $this->id,
            'lesson_id' => $this->lesson_id,
            'last_position_seconds' => $this->last_position_seconds,
            'total_watched_seconds' => $this->total_watched_seconds,
            'status' => $this->status,
            'completed_at' => $this->completed_at?->toISOString(),
        ];
    }
}
