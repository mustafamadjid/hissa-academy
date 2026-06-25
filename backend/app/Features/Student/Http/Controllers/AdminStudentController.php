<?php

namespace App\Features\Student\Http\Controllers;

use App\Features\Student\Exceptions\StudentOperationException;
use App\Features\Student\Http\Requests\StudentListRequest;
use App\Features\Student\Http\Requests\StudentPaginationRequest;
use App\Features\Student\Http\Resources\StudentCertificateResource;
use App\Features\Student\Http\Resources\StudentDetailResource;
use App\Features\Student\Http\Resources\StudentListResource;
use App\Features\Student\Http\Resources\StudentProgressCourseResource;
use App\Features\Student\Services\StudentService;
use App\GlobalExceptions\AuthorizationException;
use Illuminate\Http\JsonResponse;

final class AdminStudentController
{
    public function index(
        StudentListRequest $request,
        StudentService $studentService,
    ): JsonResponse {
        try {
            $students = $studentService->all($request->toDTO(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Daftar student berhasil diambil.',
                'data' => StudentListResource::collection($students),
                'meta' => $this->paginationMeta($students),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (StudentOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function show(
        string $student_uuid,
        StudentService $studentService,
    ): JsonResponse {
        try {
            $student = $studentService->detail($student_uuid, request()->user());

            if ($student === null) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail student berhasil diambil.',
                'data' => new StudentDetailResource($student),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (StudentOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function progress(
        string $student_uuid,
        StudentService $studentService,
    ): JsonResponse {
        try {
            $progress = $studentService->progress($student_uuid, request()->user());

            if ($progress === null) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Progress student berhasil diambil.',
                'data' => StudentProgressCourseResource::collection($progress),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (StudentOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function certificates(
        StudentPaginationRequest $request,
        string $student_uuid,
        StudentService $studentService,
    ): JsonResponse {
        try {
            $certificates = $studentService->certificates(
                $student_uuid,
                $request->toDTO(),
                $request->user()
            );

            if ($certificates === null) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Daftar sertifikat student berhasil diambil.',
                'data' => StudentCertificateResource::collection($certificates),
                'meta' => $this->paginationMeta($certificates),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (StudentOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    private function notFound(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Student tidak ditemukan.',
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
