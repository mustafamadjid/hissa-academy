<?php

namespace App\Features\Certificate\Http\Controllers;

use App\Features\Certificate\Exceptions\CertificateOperationException;
use App\Features\Certificate\Http\Requests\CertificateListRequest;
use App\Features\Certificate\Http\Requests\CertificateRevokeRequest;
use App\Features\Certificate\Http\Resources\CertificateResource;
use App\Features\Certificate\Services\CertificateService;
use App\GlobalExceptions\AuthorizationException;
use Illuminate\Http\JsonResponse;

final class CertificateController
{
    public function index(
        CertificateListRequest $request,
        CertificateService $certificateService,
    ): JsonResponse {
        try {
            $certificates = $certificateService->all($request->toDTO(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Daftar sertifikat berhasil diambil.',
                'data' => CertificateResource::collection($certificates),
                'meta' => $this->paginationMeta($certificates),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (CertificateOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function revoke(
        CertificateRevokeRequest $request,
        string $certificate_uuid,
        CertificateService $certificateService,
    ): JsonResponse {
        try {
            $certificate = $certificateService->revoke(
                $certificate_uuid,
                $request->toDTO(),
                $request->user()
            );

            if ($certificate === null) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Sertifikat berhasil dicabut.',
                'data' => new CertificateResource($certificate),
            ]);
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
