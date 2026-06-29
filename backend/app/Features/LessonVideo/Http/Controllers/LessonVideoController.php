<?php

namespace App\Features\LessonVideo\Http\Controllers;

use App\Features\LessonVideo\Exceptions\LessonVideoOperationException;
use App\Features\LessonVideo\Http\Requests\LessonVideoUpsertRequest;
use App\Features\LessonVideo\Http\Resources\LessonVideoResource;
use App\Features\LessonVideo\Services\LessonVideoManagementService;
use App\GlobalExceptions\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class LessonVideoController
{
    public function update(
        LessonVideoUpsertRequest $request,
        string $lesson_uuid,
        LessonVideoManagementService $lessonVideoService,
    ): JsonResponse {
        try {
            $video = $lessonVideoService->saveMetadata($lesson_uuid, $request->toDTO(), $request->user());

            if ($video === null) {
                return $this->lessonNotFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Metadata lesson video berhasil disimpan.',
                'data' => new LessonVideoResource($video),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (LessonVideoOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function destroy(
        Request $request,
        string $lesson_uuid,
        LessonVideoManagementService $lessonVideoService,
    ): JsonResponse {
        try {
            $deleted = $lessonVideoService->deleteMetadata($lesson_uuid, $request->user());

            if ($deleted === null) {
                return $this->lessonNotFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Metadata video lesson berhasil dihapus.',
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (LessonVideoOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    private function lessonNotFound(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Lesson tidak ditemukan.',
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
}
