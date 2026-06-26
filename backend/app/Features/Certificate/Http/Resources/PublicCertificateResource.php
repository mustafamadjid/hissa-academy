<?php

namespace App\Features\Certificate\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PublicCertificateResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'certificate_number' => $this->certificate_number,
            'status' => $this->status,
            'issued_at' => $this->issued_at?->toISOString(),
            'revoked_at' => $this->revoked_at?->toISOString(),
            'participant_name' => $this->user?->full_name,
            'course' => [
                'name' => $this->course?->course_name,
            ],
            'verification_url' => url('/verify/'.$this->certificate_number),
        ];
    }
}
