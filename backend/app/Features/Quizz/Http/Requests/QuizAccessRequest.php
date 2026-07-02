<?php

namespace App\Features\Quizz\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class QuizAccessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'course_uuid' => ['required', 'uuid'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'course_uuid' => $this->route('course_uuid'),
        ]);
    }
}
