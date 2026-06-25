<?php

namespace App\Features\Certificate\Contracts;

use App\Features\Certificate\DTOs\CertificateListData;
use App\Features\Certificate\Models\Certificate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CertificateRepositoryContract
{
    public function all(CertificateListData $query): LengthAwarePaginator;

    public function findById(string $certificateId): ?Certificate;

    public function revoke(Certificate $certificate, string $reason): Certificate;
}
