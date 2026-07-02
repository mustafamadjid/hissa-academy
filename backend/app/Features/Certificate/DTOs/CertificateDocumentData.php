<?php

namespace App\Features\Certificate\DTOs;

use Carbon\CarbonImmutable;

final readonly class CertificateDocumentData
{
    public function __construct(
        public string $studentName,
        public string $courseName,
        public string $certificateNumber,
        public CarbonImmutable $issuedAt,
        public CarbonImmutable $validUntil,
        public string $verificationUrl,
        public string $qrCodeDataUri,
    ) {}
}
