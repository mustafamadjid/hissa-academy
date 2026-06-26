<?php

namespace App\Features\Certificate\Services;

use App\Features\Certificate\Contracts\CertificateRepositoryContract;
use App\Features\Certificate\DTOs\CertificateListData;
use App\Features\Certificate\Exceptions\CertificateOperationException;
use App\Features\Certificate\Models\Certificate;
use App\Features\User\Models\User;
use App\Helper\EnsureStudentForService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Throwable;

final class StudentCertificateService
{
    public function __construct(
        private readonly CertificateRepositoryContract $certificateRepository,
        private readonly EnsureStudentForService $ensureStudent,
    ) {}

    public function list(CertificateListData $query, ?User $actor): LengthAwarePaginator
    {
        $this->ensureStudent->ensureStudent($actor);

        try {
            return $this->certificateRepository->forUser($actor->id, $query);
        } catch (Throwable $exception) {
            throw new CertificateOperationException('Gagal mengambil daftar sertifikat.', $exception);
        }
    }

    public function detail(string $certificateId, ?User $actor): ?Certificate
    {
        $this->ensureStudent->ensureStudent($actor);

        try {
            return $this->certificateRepository->findForUser($certificateId, $actor->id);
        } catch (Throwable $exception) {
            throw new CertificateOperationException('Gagal mengambil detail sertifikat.', $exception);
        }
    }
}
