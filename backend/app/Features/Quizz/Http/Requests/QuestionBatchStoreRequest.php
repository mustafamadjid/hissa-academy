<?php

namespace App\Features\Quizz\Http\Requests;

use App\Features\Quizz\DTOs\QuestionCreateData;
use App\Features\Quizz\DTOs\QuestionOptionCreateData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class QuestionBatchStoreRequest extends FormRequest
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
            'questions.*.quizz_id' => ['nullable', 'uuid'],
            'questions.*.question' => ['required', 'string', 'max:255'],
            'questions.*.position' => ['required', 'integer', 'min:1'],
            'questions.*.image_url' => ['nullable', 'string', 'max:255'],
            'questions.*.answers' => ['required', 'array', 'min:1'],
            'questions.*.answers.*.answer' => ['required', 'string', 'max:255'],
            'questions.*.answers.*.is_correct' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<int, QuestionCreateData>
     */
    public function toDTOs(): array
    {
        $validated = $this->validated();

        return array_map(
            fn (array $question): QuestionCreateData => new QuestionCreateData(
                question: $question['question'],
                position: (int) $question['position'],
                imageUrl: $question['image_url'] ?? null,
                answers: array_map(
                    fn (array $answer): QuestionOptionCreateData => new QuestionOptionCreateData(
                        answer: $answer['answer'],
                        isCorrect: (bool) $answer['is_correct'],
                    ),
                    $question['answers'],
                ),
            ),
            $validated['questions'],
        );
    }
}
