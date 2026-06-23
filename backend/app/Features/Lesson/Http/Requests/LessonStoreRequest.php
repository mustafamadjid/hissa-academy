<?php

namespace App\Features\Lesson\Http\Requests;

use App\Features\Lesson\DTOs\LessonCreateData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LessonStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'youtube_video_id' => ['required', 'string', 'regex:/^[A-Za-z0-9_-]{6,20}$/'],
            'position' => ['required', 'integer', 'min:1'],
            'is_required' => ['required', 'boolean'],
            'duration_seconds' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function toDTO(): LessonCreateData
    {
        $validated = $this->validated();

        return new LessonCreateData(
            title: $validated['title'],
            youtubeVideoId: $validated['youtube_video_id'],
            position: (int) $validated['position'],
            isRequired: (bool) $validated['is_required'],
            durationSeconds: (int) ($validated['duration_seconds'] ?? 0),
        );
    }
}
