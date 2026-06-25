<?php

namespace App\Features\Quizz\Http\Requests;

use App\Features\Quizz\DTOs\QuizzCreateData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class QuizzUpdateRequest extends FormRequest
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
            'quiz_name' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function toDTO(): QuizzCreateData
    {
        $validated = $this->validated();

        return new QuizzCreateData(
            quizName: $validated['quiz_name'],
            isActive: (bool) $validated['is_active'],
        );
    }
}
