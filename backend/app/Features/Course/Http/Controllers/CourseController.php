<?php

namespace App\Features\Course\Http\Controllers;

use App\Features\Course\Exceptions\CourseOperationException;
use App\Features\Course\Http\Requests\CourseListRequest;
use App\Features\Course\Http\Requests\CourseStoreRequest;
use App\Features\Course\Http\Requests\CourseUpdateRequest;
use App\Features\Course\Http\Resources\CourseResource;
use App\Features\Course\Services\CourseService;
use App\GlobalExceptions\AuthorizatrionException;
use Illuminate\Http\JsonResponse;

final class CourseController
{
    public function index(
        CourseListRequest $request,
        CourseService $courseService,
    ): JsonResponse {
        try {
            $courses = $courseService->all($request->toDTO());

            return response()->json([
                'success' => true,
                'message' => 'Daftar course berhasil diambil.',
                'data' => CourseResource::collection($courses),
                'meta' => [
                    'current_page' => $courses->currentPage(),
                    'per_page' => $courses->perPage(),
                    'total' => $courses->total(),
                    'last_page' => $courses->lastPage(),
                ],
            ]);
        } catch (CourseOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function show(
        string $course_uuid,
        CourseService $courseService,
    ): JsonResponse {
        try {
            $course = $courseService->findById($course_uuid);

            if ($course === null) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail course berhasil diambil.',
                'data' => new CourseResource($course),
            ]);
        } catch (CourseOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function store(
        CourseStoreRequest $request,
        CourseService $courseService,
    ): JsonResponse {
        try {
            $course = $courseService->create($request->validated(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Course berhasil dibuat.',
                'data' => new CourseResource($course),
            ], 201);
        } catch (AuthorizatrionException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (CourseOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function update(
        CourseUpdateRequest $request,
        string $course_uuid,
        CourseService $courseService,
    ): JsonResponse {
        try {
            $course = $courseService->update($course_uuid, $request->validated(), $request->user());

            if ($course === null) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Course berhasil diperbarui.',
                'data' => new CourseResource($course),
            ]);
        } catch (AuthorizatrionException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (CourseOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function destroy(
        string $course_uuid,
        CourseService $courseService,
    ): JsonResponse {
        try {
            $deleted = $courseService->delete($course_uuid, request()->user());

            if (! $deleted) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Course berhasil dihapus.',
            ]);
        } catch (AuthorizatrionException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (CourseOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    private function notFound(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Course tidak ditemukan.',
        ], 404);
    }

    private function serverError(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 500);
    }

    private function forbidden(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 403);
    }
}
