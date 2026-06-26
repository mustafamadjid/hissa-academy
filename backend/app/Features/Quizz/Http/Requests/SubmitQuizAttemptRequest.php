<?php

namespace App\Features\Quizz\Http\Requests;

use App\Features\Quizz\DTOs\SubmittedQuizAnswerData;
use App\Features\Quizz\DTOs\SubmitQuizAttemptData;
use Illuminate\Foundation\Http\FormRequest;

final class SubmitQuizAttemptRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.question_uuid' => ['required', 'uuid'],
            'answers.*.selected_option_uuid' => ['required', 'uuid'],
            'score' => ['prohibited'],
            'status' => ['prohibited'],
            'passed' => ['prohibited'],
            'is_correct' => ['prohibited'],
            'certificate_uuid' => ['prohibited'],
        ];
    }

    public function toDTO(): SubmitQuizAttemptData
    {
        $validated = $this->validated();

        return new SubmitQuizAttemptData(
            answers: array_map(
                fn (array $answer): SubmittedQuizAnswerData => new SubmittedQuizAnswerData(
                    questionUuid: $answer['question_uuid'],
                    selectedOptionUuid: $answer['selected_option_uuid'],
                ),
                $validated['answers'],
            ),
        );
    }
}
