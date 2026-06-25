<?php

namespace App\Features\Certificate\DTOs;

final readonly class CertificateListData
{
    public function __construct(
        public int $limit,
        public int $page,
    ) {}
}
