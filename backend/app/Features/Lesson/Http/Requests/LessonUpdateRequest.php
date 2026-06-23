<?php

namespace App\Features\Lesson\Http\Requests;

use App\Features\Lesson\DTOs\LessonUpdateData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LessonUpdateRequest extends FormRequest
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
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'position' => ['sometimes', 'required', 'integer', 'min:1'],
            'is_required' => ['sometimes', 'required', 'boolean'],
        ];
    }

    public function toDTO(): LessonUpdateData
    {
        $validated = $this->validated();

        return new LessonUpdateData(
            title: $validated['title'] ?? null,
            position: array_key_exists('position', $validated) ? (int) $validated['position'] : null,
            isRequired: array_key_exists('is_required', $validated) ? (bool) $validated['is_required'] : null,
        );
    }
}
