<?php

namespace App\Features\Quizz\Http\Requests;

use App\Features\Quizz\DTOs\QuestionOptionCreateData;
use App\Features\Quizz\DTOs\QuestionUpdateData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class QuestionUpdateRequest extends FormRequest
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
            'question' => ['required', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:1'],
            'position' => ['required', 'integer', 'min:1'],
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.answer' => ['required', 'string', 'max:255'],
            'answers.*.is_correct' => ['required', 'boolean'],
        ];
    }

    public function toDTO(): QuestionUpdateData
    {
        $validated = $this->validated();

        return new QuestionUpdateData(
            question: $validated['question'],
            points: (int) $validated['points'],
            position: (int) $validated['position'],
            answers: array_map(
                fn (array $answer): QuestionOptionCreateData => new QuestionOptionCreateData(
                    answer: $answer['answer'],
                    isCorrect: (bool) $answer['is_correct'],
                ),
                $validated['answers'],
            ),
        );
    }
}
