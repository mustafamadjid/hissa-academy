<?php

namespace App\Features\Certificate\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class StudentCertificateResource extends JsonResource
{
    public function __construct(
        mixed $resource,
        private readonly bool $withDetail = false,
    ) {
        parent::__construct($resource);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_filter([
            'uuid' => $this->id,
            'certificate_number' => $this->certificate_number,
            'course' => [
                'uuid' => $this->course?->id,
                'name' => $this->course?->course_name,
            ],
            'issued_at' => $this->issued_at?->toISOString(),
            'valid_until' => $this->issued_at?->copy()->addYears(3)->toISOString(),
            'status' => $this->status,
            'participant_name' => $this->when($this->withDetail, $this->user?->full_name),
            'verification_url' => $this->when($this->withDetail, url('/verify/'.$this->certificate_number)),
            'download_url' => $this->when($this->withDetail, url("/api/v1/certificates/{$this->id}/file")),
        ], fn ($value): bool => ! $value instanceof \Illuminate\Http\Resources\MissingValue);
    }
}
