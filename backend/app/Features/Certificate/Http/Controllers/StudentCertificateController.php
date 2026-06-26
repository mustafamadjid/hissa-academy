<?php

namespace App\Features\Certificate\Http\Controllers;

use App\Features\Certificate\Exceptions\CertificateOperationException;
use App\Features\Certificate\Http\Requests\CertificateListRequest;
use App\Features\Certificate\Http\Resources\StudentCertificateResource;
use App\Features\Certificate\Services\StudentCertificateService;
use App\GlobalExceptions\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class StudentCertificateController
{
    public function index(
        CertificateListRequest $request,
        StudentCertificateService $certificateService,
    ): JsonResponse {
        try {
            $certificates = $certificateService->list($request->toDTO(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Daftar sertifikat berhasil diambil.',
                'data' => StudentCertificateResource::collection($certificates),
                'meta' => $this->paginationMeta($certificates),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (CertificateOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function show(
        string $certificate_uuid,
        CertificateListRequest $request,
        StudentCertificateService $certificateService,
    ): JsonResponse {
        try {
            $certificate = $certificateService->detail($certificate_uuid, $request->user());

            if ($certificate === null) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail sertifikat berhasil diambil.',
                'data' => new StudentCertificateResource($certificate, true),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (CertificateOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function download(
        string $certificate_uuid,
        CertificateListRequest $request,
        StudentCertificateService $certificateService,
    ): JsonResponse|StreamedResponse {
        try {
            $certificate = $certificateService->detail($certificate_uuid, $request->user());

            if ($certificate === null || ! Storage::disk('local')->exists($certificate->pdf_path)) {
                return $this->notFound();
            }

            return Storage::disk('local')->download(
                $certificate->pdf_path,
                $certificate->certificate_number.'.pdf',
                ['Content-Type' => 'application/pdf'],
            );
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (CertificateOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    private function notFound(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Sertifikat tidak ditemukan.',
        ], 404);
    }

    private function forbidden(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 403);
    }

    private function serverError(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 500);
    }

    /**
     * @return array<string, int>
     */
    private function paginationMeta(mixed $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
