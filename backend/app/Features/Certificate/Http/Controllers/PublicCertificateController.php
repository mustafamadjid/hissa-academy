<?php

namespace App\Features\Certificate\Http\Controllers;

use App\Features\Certificate\Exceptions\CertificateOperationException;
use App\Features\Certificate\Http\Resources\PublicCertificateResource;
use App\Features\Certificate\Services\PublicCertificateService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class PublicCertificateController
{
    public function show(
        string $certificate_number,
        PublicCertificateService $certificateService,
    ): JsonResponse {
        try {
            $certificate = $certificateService->findByCertificateNumber($certificate_number);

            if ($certificate === null) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Status sertifikat berhasil diambil.',
                'data' => new PublicCertificateResource($certificate),
            ]);
        } catch (CertificateOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function verify(
        string $certificate_number,
        PublicCertificateService $certificateService,
    ): View|Response {
        $certificate = $certificateService->findByCertificateNumber($certificate_number);

        $view = view('certificates.verify', [
            'certificate' => $certificate,
            'certificateNumber' => $certificate_number,
        ]);

        if ($certificate === null) {
            return response($view, 404);
        }

        return $view;
    }

    private function notFound(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Sertifikat tidak ditemukan.',
        ], 404);
    }

    private function serverError(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 500);
    }
}
