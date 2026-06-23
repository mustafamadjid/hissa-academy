<?php

namespace App\Features\Lesson\Http\Controllers;

use App\Features\Lesson\Exceptions\LessonOperationException;
use App\Features\Lesson\Http\Requests\LessonStoreRequest;
use App\Features\Lesson\Http\Requests\LessonUpdateRequest;
use App\Features\Lesson\Http\Resources\LessonResource;
use App\Features\Lesson\Services\LessonService;
use App\GlobalExceptions\AuthorizationException;
use Illuminate\Http\JsonResponse;

final class LessonController
{
    public function index(
        string $course_uuid,
        LessonService $lessonService,
    ): JsonResponse {
        try {
            $lessons = $lessonService->listByCourse($course_uuid);

            if ($lessons === null) {
                return $this->courseNotFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Daftar lesson berhasil diambil.',
                'data' => LessonResource::collection($lessons),
            ]);
        } catch (LessonOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function store(
        LessonStoreRequest $request,
        string $course_uuid,
        LessonService $lessonService,
    ): JsonResponse {
        try {
            $lesson = $lessonService->create($course_uuid, $request->toDTO(), $request->user());

            if ($lesson === null) {
                return $this->courseNotFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Lesson berhasil dibuat.',
                'data' => new LessonResource($lesson),
            ], 201);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (LessonOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function show(
        string $lesson_uuid,
        LessonService $lessonService,
    ): JsonResponse {
        try {
            $lesson = $lessonService->findById($lesson_uuid);

            if ($lesson === null) {
                return $this->lessonNotFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail lesson berhasil diambil.',
                'data' => new LessonResource($lesson),
            ]);
        } catch (LessonOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function update(
        LessonUpdateRequest $request,
        string $lesson_uuid,
        LessonService $lessonService,
    ): JsonResponse {
        try {
            $lesson = $lessonService->update($lesson_uuid, $request->toDTO(), $request->user());

            if ($lesson === null) {
                return $this->lessonNotFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Lesson berhasil diperbarui.',
                'data' => new LessonResource($lesson),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (LessonOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function destroy(
        string $lesson_uuid,
        LessonService $lessonService,
    ): JsonResponse {
        try {
            $deleted = $lessonService->delete($lesson_uuid, request()->user());

            if ($deleted === null) {
                return $this->lessonNotFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Lesson berhasil dihapus.',
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (LessonOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    private function courseNotFound(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Course tidak ditemukan.',
        ], 404);
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
