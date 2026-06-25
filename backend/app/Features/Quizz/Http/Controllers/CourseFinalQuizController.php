<?php

namespace App\Features\Quizz\Http\Controllers;

use App\Features\Quizz\Exceptions\QuizzOperationException;
use App\Features\Quizz\Http\Requests\QuizzStoreRequest;
use App\Features\Quizz\Http\Resources\QuizzResource;
use App\Features\Quizz\Services\QuizzService;
use App\GlobalExceptions\AuthorizationException;
use Illuminate\Http\JsonResponse;

final class CourseFinalQuizController
{
    public function show(string $course_uuid, QuizzService $quizzService): JsonResponse
    {
        try {
            $courseExists = $quizzService->courseExists($course_uuid);

            if (! $courseExists) {
                return $this->courseNotFound();
            }

            $quiz = $quizzService->findFinalQuizByCourse($course_uuid);

            if ($quiz === null) {
                return $this->quizNotFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Konfigurasi quiz berhasil diambil.',
                'data' => new QuizzResource($quiz),
            ]);
        } catch (QuizzOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function store(
        QuizzStoreRequest $request,
        string $course_uuid,
        QuizzService $quizzService,
    ): JsonResponse {
        try {
            $quiz = $quizzService->createFinalQuiz($course_uuid, $request->toDTO(), $request->user());

            if ($quiz === null) {
                return $this->courseNotFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Quiz berhasil dibuat.',
                'data' => new QuizzResource($quiz),
            ], 201);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (QuizzOperationException $exception) {
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

    private function quizNotFound(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Quiz tidak ditemukan.',
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
