<?php

namespace App\Features\Student\Http\Requests;

use App\Features\Student\DTOs\StudentPaginationData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class StudentPaginationRequest extends FormRequest
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
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function toDTO(): StudentPaginationData
    {
        $validated = $this->validated();

        return new StudentPaginationData(
            limit: (int) ($validated['limit'] ?? 10),
            page: (int) ($validated['page'] ?? 1),
        );
    }
}
