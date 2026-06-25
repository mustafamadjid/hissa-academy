<?php

namespace App\Features\Certificate\DTOs;

final readonly class CertificateRevokeData
{
    public function __construct(
        public string $reason,
    ) {}
}
