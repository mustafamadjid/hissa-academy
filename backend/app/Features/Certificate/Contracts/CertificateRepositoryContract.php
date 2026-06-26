<?php

namespace App\Features\Certificate\Contracts;

use App\Features\Certificate\DTOs\CertificateListData;
use App\Features\Certificate\Models\Certificate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CertificateRepositoryContract
{
    public function all(CertificateListData $query): LengthAwarePaginator;

    public function forUser(string $userId, CertificateListData $query): LengthAwarePaginator;

    public function findById(string $certificateId): ?Certificate;

    public function findByCertificateNumber(string $certificateNumber): ?Certificate;

    public function findForUser(string $certificateId, string $userId): ?Certificate;

    public function revoke(Certificate $certificate, string $reason): Certificate;
}
