<?php

namespace App\Features\Certificate\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CertificateResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'certificate_number' => $this->certificate_number,
            'issued_at' => $this->issued_at?->toISOString(),
            'status' => $this->status,
            'pdf_path' => $this->pdf_path,
            'revoked_reason' => $this->revoked_reason,
            'revoked_at' => $this->revoked_at?->toISOString(),
            'student' => [
                'id' => $this->user?->id,
                'full_name' => $this->user?->full_name,
                'email' => $this->user?->email,
            ],
            'course' => [
                'id' => $this->course?->id,
                'name' => $this->course?->course_name,
                'status' => $this->course?->status,
            ],
        ];
    }
}
