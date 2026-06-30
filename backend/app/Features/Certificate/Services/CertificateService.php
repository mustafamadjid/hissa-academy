<?php

namespace App\Features\Certificate\Services;

use App\Features\Certificate\Contracts\CertificateRepositoryContract;
use App\Features\Certificate\DTOs\CertificateListData;
use App\Features\Certificate\DTOs\CertificateRevokeData;
use App\Features\Certificate\Exceptions\CertificateOperationException;
use App\Features\Certificate\Models\Certificate;
use App\Features\User\Models\User;
use App\Helper\EnsureAdminForService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Throwable;

final class CertificateService
{
    public function __construct(
        private readonly CertificateRepositoryContract $certificateRepository,
        private readonly EnsureAdminForService $ensureAdmin,
    ) {}

    public function all(CertificateListData $query, ?User $actor): LengthAwarePaginator
    {
        $this->ensureAdmin->ensureAdmin($actor);

        try {
            return $this->certificateRepository->all($query);
        } catch (Throwable $exception) {
            Log::error('Gagal mengambil daftar sertifikat.', [
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new CertificateOperationException('Gagal mengambil daftar sertifikat.', $exception);
        }
    }

    public function revoke(string $certificateId, CertificateRevokeData $data, ?User $actor): ?Certificate
    {
        $this->ensureAdmin->ensureAdmin($actor);

        try {
            $certificate = $this->certificateRepository->findById($certificateId);

            if ($certificate === null) {
                return null;
            }

            return $this->certificateRepository->revoke($certificate, $data->reason);
        } catch (Throwable $exception) {
            Log::error('Gagal mencabut sertifikat.', [
                'certificate_id' => $certificateId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new CertificateOperationException('Gagal mencabut sertifikat.', $exception);
        }
    }
}
