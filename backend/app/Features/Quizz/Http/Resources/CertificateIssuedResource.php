<?php

namespace App\Features\Quizz\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CertificateIssuedResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->id,
            'certificate_number' => $this->certificate_number,
            'status' => $this->status,
            'issued_at' => $this->issued_at?->toISOString(),
        ];
    }
}
