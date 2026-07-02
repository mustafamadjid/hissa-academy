<?php

namespace App\Features\Certificate\Services;

use App\Features\Certificate\Contracts\CertificateRepositoryContract;
use App\Features\Certificate\Exceptions\CertificateOperationException;
use App\Features\Certificate\Models\Certificate;
use Illuminate\Support\Facades\Log;
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
            Log::error('Gagal mengambil status sertifikat.', [
                'certificate_number' => $certificateNumber,
                'exception' => $exception,
            ]);

            throw new CertificateOperationException('Gagal mengambil status sertifikat.', $exception);
        }
    }

    public function findFileByUuid(string $certificateUuid): ?Certificate
    {
        try {
            $certificate = $this->certificateRepository->findById($certificateUuid);

            if ($certificate === null || ! $this->isSafeDocumentPath($certificate->pdf_path)) {
                return null;
            }

            return $certificate;
        } catch (Throwable $exception) {
            Log::error('Gagal mengakses file sertifikat.', [
                'certificate_uuid' => $certificateUuid,
                'exception' => $exception,
            ]);

            throw new CertificateOperationException('Gagal mengakses file sertifikat.', $exception);
        }
    }

    private function isSafeDocumentPath(mixed $path): bool
    {
        return is_string($path)
            && ! str_contains($path, "\0")
            && preg_match('/\Acertificates\/[0-9a-f-]{36}\.pdf\z/i', $path) === 1;
    }
}
