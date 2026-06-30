<?php

namespace App\Features\Quizz\Http\Requests;

use App\Features\Quizz\DTOs\QuestionReorderData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class QuestionReorderRequest extends FormRequest
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
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.id' => ['required', 'uuid', 'distinct'],
            'questions.*.position' => ['required', 'integer', 'min:1', 'distinct'],
        ];
    }

    public function toDTO(): QuestionReorderData
    {
        return new QuestionReorderData(
            questions: $this->validated('questions'),
        );
    }
}
