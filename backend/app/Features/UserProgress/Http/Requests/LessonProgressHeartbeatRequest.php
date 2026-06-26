<?php

namespace App\Features\UserProgress\Http\Requests;

use App\Features\UserProgress\DTOs\LessonProgressHeartbeatData;
use Illuminate\Foundation\Http\FormRequest;

final class LessonProgressHeartbeatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'last_position_seconds' => 'required|integer|min:0',
            'watched_seconds' => 'required|integer|min:0',
        ];
    }

    public function toDTO(): LessonProgressHeartbeatData
    {
        $validated = $this->validated();

        return new LessonProgressHeartbeatData(
            lastPositionSeconds: (int) $validated['last_position_seconds'],
            watchedSeconds: (int) $validated['watched_seconds'],
        );
    }
}
