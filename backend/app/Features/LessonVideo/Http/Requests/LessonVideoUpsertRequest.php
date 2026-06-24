<?php

namespace App\Features\LessonVideo\Http\Requests;

use App\Features\LessonVideo\DTOs\LessonVideoUpsertData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class LessonVideoUpsertRequest extends FormRequest
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
            'youtube_video_id' => ['required', 'string', 'regex:/^[A-Za-z0-9_-]{6,20}$/'],
        ];
    }

    public function toDTO(): LessonVideoUpsertData
    {
        $validated = $this->validated();

        return new LessonVideoUpsertData(
            youtubeVideoId: $validated['youtube_video_id'],
        );
    }
}
