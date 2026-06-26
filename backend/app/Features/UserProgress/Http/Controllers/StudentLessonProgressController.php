<?php

namespace App\Features\UserProgress\Http\Controllers;

use App\Features\UserProgress\Exceptions\UserProgressOperationException;
use App\Features\UserProgress\Http\Requests\LessonProgressHeartbeatRequest;
use App\Features\UserProgress\Http\Resources\UserProgressResource;
use App\Features\UserProgress\Services\StudentLessonProgressService;
use App\GlobalExceptions\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class StudentLessonProgressController
{
    public function show(
        string $lesson_uuid,
        Request $request,
        StudentLessonProgressService $progressService,
    ): JsonResponse {
        try {
            $progress = $progressService->get($lesson_uuid, $request->user());

            if ($progress === null) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Progress lesson berhasil diambil.',
                'data' => new UserProgressResource($progress),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (UserProgressOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function store(
        string $lesson_uuid,
        LessonProgressHeartbeatRequest $request,
        StudentLessonProgressService $progressService,
    ): JsonResponse {
        try {
            $progress = $progressService->heartbeat($lesson_uuid, $request->toDTO(), $request->user());

            if ($progress === null) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Progress lesson berhasil disimpan.',
                'data' => new UserProgressResource($progress),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (UserProgressOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    private function notFound(): JsonResponse
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
