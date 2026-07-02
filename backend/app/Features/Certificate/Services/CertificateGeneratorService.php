<?php

namespace App\Features\Certificate\Services;

use App\Features\Certificate\Contracts\CertificateGeneratorContract;
use App\Features\Certificate\Contracts\CertificateRepositoryContract;
use App\Features\Certificate\DTOs\CertificateDocumentData;
use App\Features\Certificate\Exceptions\CertificateOperationException;
use App\Features\Certificate\Models\Certificate;
use App\Features\Course\Models\Course;
use App\Features\User\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

final class CertificateGeneratorService implements CertificateGeneratorContract
{
    private const DISK = 'private';

    public function __construct(
        private readonly CertificateRepositoryContract $certificateRepository,
    ) {}

    public function issue(User $student, Course $course): Certificate
    {
        $certificate = $this->certificateRepository->findIssuedForUserAndCourse($student->id, $course->id);

        if ($certificate !== null && $this->hasSafeExistingDocument($certificate)) {
            return $certificate;
        }

        try {
            $certificate ??= $this->certificateRepository->createIssued($student, $course);
            $path = $this->generateInternalPath();
            $pdf = $this->renderPdf($certificate->loadMissing(['user', 'course']));

            if (! Storage::disk(self::DISK)->put($path, $pdf)) {
                throw new CertificateOperationException('Gagal menyimpan dokumen sertifikat.');
            }

            return $this->certificateRepository->updatePdfPath($certificate, $path);
        } catch (CertificateOperationException $exception) {
            Log::error('Gagal generate sertifikat PDF.', [
                'student_uuid' => $student->id,
                'course_uuid' => $course->id,
                'exception' => $exception,
            ]);

            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Gagal generate sertifikat PDF.', [
                'student_uuid' => $student->id,
                'course_uuid' => $course->id,
                'exception' => $exception,
            ]);

            throw new CertificateOperationException('Gagal generate sertifikat PDF.', $exception);
        }
    }

    private function renderPdf(Certificate $certificate): string
    {
        $issuedAt = $certificate->issued_at->toImmutable();
        $verificationUrl = url('/verify/'.$certificate->certificate_number);
        $qrCode = new QrCode(
            data: $verificationUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 280,
            margin: 12,
        );
        $data = new CertificateDocumentData(
            studentName: $certificate->user->full_name,
            courseName: $certificate->course->course_name,
            certificateNumber: $certificate->certificate_number,
            issuedAt: $issuedAt,
            validUntil: $issuedAt->addYears(3),
            verificationUrl: $verificationUrl,
            qrCodeDataUri: (new PngWriter())->write($qrCode)->getDataUri(),
        );

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isPhpEnabled', false);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml(view('certificates.pdf', ['certificate' => $data])->render(), 'UTF-8');
        $dompdf->render();

        return $dompdf->output();
    }

    private function generateInternalPath(): string
    {
        return 'certificates/'.Str::uuid().'.pdf';
    }

    private function hasSafeExistingDocument(Certificate $certificate): bool
    {
        return is_string($certificate->pdf_path)
            && preg_match('/\Acertificates\/[0-9a-f-]{36}\.pdf\z/i', $certificate->pdf_path) === 1
            && Storage::disk(self::DISK)->exists($certificate->pdf_path);
    }
}
