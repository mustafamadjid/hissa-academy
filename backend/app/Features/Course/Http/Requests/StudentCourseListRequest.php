<?php

namespace App\Features\Course\Http\Requests;

use App\Features\Course\DTOs\StudentCourseListData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class StudentCourseListRequest extends FormRequest
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
            'search' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toDTO(): StudentCourseListData
    {
        $validated = $this->validated();

        return new StudentCourseListData(
            search: $validated['search'] ?? null,
            perPage: (int) ($validated['per_page'] ?? 15),
        );
    }
}
