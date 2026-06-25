<?php

namespace App\Features\Certificate\Repositories;

use App\Features\Certificate\Contracts\CertificateRepositoryContract;
use App\Features\Certificate\DTOs\CertificateListData;
use App\Features\Certificate\Models\Certificate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentCertificateRepository implements CertificateRepositoryContract
{
    public function all(CertificateListData $query): LengthAwarePaginator
    {
        return Certificate::query()
            ->with(['user', 'course'])
            ->orderByDesc('issued_at')
            ->paginate(
                $query->limit,
                [
                    'id',
                    'user_id',
                    'course_id',
                    'certificate_number',
                    'issued_at',
                    'status',
                    'pdf_path',
                    'revoked_reason',
                    'revoked_at',
                ],
                'page',
                $query->page
            );
    }

    public function findById(string $certificateId): ?Certificate
    {
        return Certificate::query()
            ->with(['user', 'course'])
            ->whereKey($certificateId)
            ->first();
    }

    public function revoke(Certificate $certificate, string $reason): Certificate
    {
        $certificate->forceFill([
            'status' => 'revoked',
            'revoked_reason' => $reason,
            'revoked_at' => now(),
        ])->save();

        return $certificate->refresh()->load(['user', 'course']);
    }
}
