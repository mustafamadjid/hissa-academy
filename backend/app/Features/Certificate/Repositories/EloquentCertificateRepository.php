<?php

namespace App\Features\Certificate\Repositories;

use App\Features\Certificate\Contracts\CertificateRepositoryContract;
use App\Features\Certificate\DTOs\CertificateListData;
use App\Features\Certificate\Models\Certificate;
use App\Features\Course\Models\Course;
use App\Features\User\Models\User;
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

    public function forUser(string $userId, CertificateListData $query): LengthAwarePaginator
    {
        return Certificate::query()
            ->with(['user', 'course'])
            ->where('user_id', $userId)
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

    public function findByCertificateNumber(string $certificateNumber): ?Certificate
    {
        return Certificate::query()
            ->with(['user', 'course'])
            ->where('certificate_number', $certificateNumber)
            ->first();
    }

    public function findForUser(string $certificateId, string $userId): ?Certificate
    {
        return Certificate::query()
            ->with(['user', 'course'])
            ->where('user_id', $userId)
            ->whereKey($certificateId)
            ->first();
    }

    public function findIssuedForUserAndCourse(string $userId, string $courseId): ?Certificate
    {
        return Certificate::query()
            ->with(['user', 'course'])
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('status', 'issued')
            ->first();
    }

    public function createIssued(User $user, Course $course): Certificate
    {
        return Certificate::query()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'issued_at' => now(),
            'status' => 'issued',
            'pdf_path' => '',
        ])->load(['user', 'course']);
    }

    public function updatePdfPath(Certificate $certificate, string $pdfPath): Certificate
    {
        $certificate->forceFill(['pdf_path' => $pdfPath])->save();

        return $certificate->refresh()->load(['user', 'course']);
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
