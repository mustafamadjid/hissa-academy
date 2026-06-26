<?php

namespace App\Features\Lesson\Http\Controllers;

use App\Features\Lesson\Exceptions\LessonOperationException;
use App\Features\Lesson\Http\Resources\StudentLessonResource;
use App\Features\Lesson\Services\StudentLessonService;
use App\GlobalExceptions\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class StudentLessonController
{
    public function show(string $lesson_uuid, Request $request, StudentLessonService $lessonService): JsonResponse
    {
        try {
            $lesson = $lessonService->detail($lesson_uuid, $request->user());

            if ($lesson === null) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail lesson berhasil diambil.',
                'data' => new StudentLessonResource($lesson),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (LessonOperationException $exception) {
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
