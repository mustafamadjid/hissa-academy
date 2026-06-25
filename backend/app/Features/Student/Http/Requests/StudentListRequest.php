<?php

namespace App\Features\Student\Http\Requests;

use App\Features\Student\DTOs\StudentListQueryData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StudentListRequest extends FormRequest
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
            'email_verified' => ['nullable', 'boolean'],
            'sort_by' => ['nullable', 'string', Rule::in([
                'full_name',
                'email',
                'last_login_at',
                'created_at',
                'updated_at',
            ])],
            'sort_direction' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function toDTO(): StudentListQueryData
    {
        $validated = $this->validated();

        return new StudentListQueryData(
            search: $validated['search'] ?? null,
            emailVerified: array_key_exists('email_verified', $validated)
                ? $this->boolean('email_verified')
                : null,
            sortBy: $validated['sort_by'] ?? 'created_at',
            sortDirection: $validated['sort_direction'] ?? 'desc',
            limit: (int) ($validated['limit'] ?? 10),
            page: (int) ($validated['page'] ?? 1),
        );
    }
}
