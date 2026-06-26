<?php

namespace App\Features\Course\Http\Controllers;

use App\Features\Course\Exceptions\CourseOperationException;
use App\Features\Course\Http\Resources\StudentCourseResource;
use App\Features\Course\Services\StudentCourseService;
use App\GlobalExceptions\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class StudentCourseController
{
    public function index(Request $request, StudentCourseService $courseService): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Daftar course berhasil diambil.',
                'data' => StudentCourseResource::collection($courseService->listAvailable($request->user())),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (CourseOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function show(string $course_uuid, Request $request, StudentCourseService $courseService): JsonResponse
    {
        try {
            $course = $courseService->detail($course_uuid, $request->user());

            if ($course === null) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail course berhasil diambil.',
                'data' => new StudentCourseResource($course),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (CourseOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function progress(string $course_uuid, Request $request, StudentCourseService $courseService): JsonResponse
    {
        try {
            $progress = $courseService->progress($course_uuid, $request->user());

            if ($progress === null) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Progress course berhasil diambil.',
                'data' => $progress,
            ]);
        } catch (AuthorizationException $exception) {
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
