<?php

namespace App\Features\Quizz\Http\Controllers;

use App\Features\Quizz\Exceptions\QuizzOperationException;
use App\Features\Quizz\Http\Requests\QuestionBatchStoreRequest;
use App\Features\Quizz\Http\Requests\QuestionUpdateRequest;
use App\Features\Quizz\Http\Requests\QuizzUpdateRequest;
use App\Features\Quizz\Http\Resources\QuestionResource;
use App\Features\Quizz\Http\Resources\QuizzResource;
use App\Features\Quizz\Services\QuizzService;
use App\GlobalExceptions\AuthorizationException;
use Illuminate\Http\JsonResponse;

final class AdminQuizController
{
    public function update(
        QuizzUpdateRequest $request,
        string $quiz_uuid,
        QuizzService $quizzService,
    ): JsonResponse {
        try {
            $quiz = $quizzService->updateQuiz($quiz_uuid, $request->toDTO(), $request->user());

            if ($quiz === null) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Quiz berhasil diperbarui.',
                'data' => new QuizzResource($quiz),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (QuizzOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function destroy(string $quiz_uuid, QuizzService $quizzService): JsonResponse
    {
        try {
            $deleted = $quizzService->deleteQuiz($quiz_uuid, request()->user());

            if (! $deleted) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Quiz berhasil dinonaktifkan.',
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (QuizzOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function questions(string $quiz_uuid, QuizzService $quizzService): JsonResponse
    {
        try {
            $questions = $quizzService->listQuestions($quiz_uuid, request()->user());

            if ($questions === null) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Daftar pertanyaan quiz berhasil diambil.',
                'data' => QuestionResource::collection($questions),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (QuizzOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function storeQuestions(
        QuestionBatchStoreRequest $request,
        string $quiz_uuid,
        QuizzService $quizzService,
    ): JsonResponse {
        try {
            $questions = $quizzService->createQuestions($quiz_uuid, $request->toDTOs(), $request->user());

            if ($questions === null) {
                return $this->notFound();
            }

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan quiz berhasil ditambahkan.',
                'data' => QuestionResource::collection($questions),
            ], 201);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (QuizzOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    public function updateQuestion(
        QuestionUpdateRequest $request,
        string $question_uuid,
        QuizzService $quizzService,
    ): JsonResponse {
        try {
            $question = $quizzService->updateQuestion($question_uuid, $request->toDTO(), $request->user());

            if ($question === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pertanyaan quiz tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan quiz berhasil diperbarui.',
                'data' => new QuestionResource($question),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->forbidden($exception->getMessage());
        } catch (QuizzOperationException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }

    private function notFound(): JsonResponse
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
