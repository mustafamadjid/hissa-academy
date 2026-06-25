<?php

namespace App\Features\Student\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class StudentCertificateResource extends JsonResource
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
            'course' => [
                'id' => $this->course?->id,
                'name' => $this->course?->course_name,
                'status' => $this->course?->status,
            ],
        ];
    }
}
