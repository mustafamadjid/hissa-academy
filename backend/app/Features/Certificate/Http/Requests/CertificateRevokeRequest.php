<?php

namespace App\Features\Certificate\Http\Requests;

use App\Features\Certificate\DTOs\CertificateRevokeData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class CertificateRevokeRequest extends FormRequest
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
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }

    public function toDTO(): CertificateRevokeData
    {
        $validated = $this->validated();

        return new CertificateRevokeData(
            reason: $validated['reason'],
        );
    }
}
