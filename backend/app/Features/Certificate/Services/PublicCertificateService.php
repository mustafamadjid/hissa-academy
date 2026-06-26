<?php

namespace App\Features\Certificate\Services;

use App\Features\Certificate\Contracts\CertificateRepositoryContract;
use App\Features\Certificate\Exceptions\CertificateOperationException;
use App\Features\Certificate\Models\Certificate;
use Throwable;

final class PublicCertificateService
{
    public function __construct(
        private readonly CertificateRepositoryContract $certificateRepository,
    ) {}

    public function findByCertificateNumber(string $certificateNumber): ?Certificate
    {
        try {
            return $this->certificateRepository->findByCertificateNumber($certificateNumber);
        } catch (Throwable $exception) {
            throw new CertificateOperationException('Gagal mengambil status sertifikat.', $exception);
        }
    }
}
