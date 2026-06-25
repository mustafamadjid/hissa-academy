<?php

namespace App\Features\Certificate\Http\Requests;

use App\Features\Certificate\DTOs\CertificateListData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class CertificateListRequest extends FormRequest
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

    public function toDTO(): CertificateListData
    {
        $validated = $this->validated();

        return new CertificateListData(
            limit: (int) ($validated['limit'] ?? 10),
            page: (int) ($validated['page'] ?? 1),
        );
    }
}
