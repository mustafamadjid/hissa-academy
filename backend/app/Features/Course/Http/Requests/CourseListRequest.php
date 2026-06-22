<?php

namespace App\Features\Course\Http\Requests;

use App\Features\Course\DTOs\CourseListQueryData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseListRequest extends FormRequest
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
            'sort_by' => ['nullable', 'string', Rule::in([
                'course_name',
                'minimum_score',
                'status',
                'created_at',
                'updated_at',
            ])],
            'sort_direction' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function toDTO(): CourseListQueryData
    {
        $validated = $this->validated();

        return new CourseListQueryData(
            search: $validated['search'] ?? null,
            sortBy: $validated['sort_by'] ?? 'created_at',
            sortDirection: $validated['sort_direction'] ?? 'desc',
            limit: (int) ($validated['limit'] ?? 10),
            page: (int) ($validated['page'] ?? 1),
        );
    }
}
