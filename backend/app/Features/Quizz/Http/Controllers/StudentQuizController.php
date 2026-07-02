<?php

namespace App\Features\Quizz\Http\Controllers;

use App\Features\Quizz\Exceptions\StudentQuizOperationException;
use App\Features\Quizz\Http\Requests\QuizAccessRequest;
use App\Features\Quizz\Http\Requests\SubmitQuizAttemptRequest;
use App\Features\Quizz\Http\Resources\QuizAccessResource;
use App\Features\Quizz\Http\Resources\QuizAttemptResource;
use App\Features\Quizz\Http\Resources\QuizSubmitResultResource;
use App\Features\Quizz\Http\Resources\StudentQuizResource;
use App\Features\Quizz\Services\StudentQuizService;
use App\GlobalExceptions\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class StudentQuizController
{
    public function access(string $course_uuid, QuizAccessRequest $request, StudentQuizService $quizService): JsonResponse
    {
        try {
            $access = $quizService->quizAccess($course_uuid, $request->user());

            if ($access === null) {
                return $this->notFound('Quiz tidak ditemukan.');
            }

            return response()->json([
                'success' => true,
                'message' => 'Akses quiz berhasil diperiksa.',
                'data' => new QuizAccessResource($access),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 403);
        } catch (StudentQuizOperationException $exception) {
            return $this->operationError($exception);
        }
    }

    public function courseQuiz(string $course_uuid, Request $request, StudentQuizService $quizService): JsonResponse
    {
        try {
            $result = $quizService->courseQuiz($course_uuid, $request->user());

            if ($result === null) {
                return $this->notFound('Quiz tidak ditemukan.');
            }

            return response()->json([
                'success' => true,
                'message' => 'Quiz berhasil diambil.',
                'data' => new StudentQuizResource($result['quiz'], $result['attempts_used']),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 403);
        } catch (StudentQuizOperationException $exception) {
            return $this->operationError($exception);
        }
    }

    public function createAttempt(string $quiz_uuid, Request $request, StudentQuizService $quizService): JsonResponse
    {
        try {
            $attempt = $quizService->createAttempt($quiz_uuid, $request->user());

            if ($attempt === null) {
                return $this->notFound('Quiz tidak ditemukan.');
            }

            return response()->json([
                'success' => true,
                'message' => 'Quiz attempt berhasil dibuat.',
                'data' => new QuizAttemptResource($attempt),
            ], 201);
        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 403);
        } catch (StudentQuizOperationException $exception) {
            return $this->operationError($exception);
        }
    }

    public function attempt(string $attempt_uuid, Request $request, StudentQuizService $quizService): JsonResponse
    {
        try {
            $attempt = $quizService->attempt($attempt_uuid, $request->user());

            if ($attempt === null) {
                return $this->notFound('Quiz attempt tidak ditemukan.');
            }

            return response()->json([
                'success' => true,
                'message' => 'Quiz attempt berhasil diambil.',
                'data' => new QuizAttemptResource($attempt),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 403);
        } catch (StudentQuizOperationException $exception) {
            return $this->operationError($exception);
        }
    }

    public function submitAttempt(
        SubmitQuizAttemptRequest $request,
        string $attempt_uuid,
        StudentQuizService $quizService,
    ): JsonResponse {
        try {
            $result = $quizService->submitAttempt($attempt_uuid, $request->toDTO(), $request->user());

            if ($result === null) {
                return $this->notFound('Quiz attempt tidak ditemukan.');
            }

            $passed = $result['attempt']->status === 'passed';

            return response()->json([
                'success' => true,
                'message' => $passed
                    ? 'Quiz berhasil disubmit dan user dinyatakan lulus.'
                    : 'Quiz berhasil disubmit, tetapi nilai belum memenuhi batas kelulusan.',
                'data' => new QuizSubmitResultResource($result),
            ]);
        } catch (AuthorizationException $exception) {
            return $this->error($exception->getMessage(), 403);
        } catch (StudentQuizOperationException $exception) {
            return $this->operationError($exception);
        }
    }

    private function notFound(string $message): JsonResponse
    {
        return $this->error($message, 404);
    }

    private function operationError(StudentQuizOperationException $exception): JsonResponse
    {
        if ($exception->statusCode() >= 500) {
            report($exception);
        }

        return $this->error(
            $exception->getMessage(),
            $exception->statusCode(),
            $exception->errorCode(),
            $exception->details(),
        );
    }

    /**
     * @param  array<string, mixed>  $details
     */
    private function error(string $message, int $status, ?string $code = null, array $details = []): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($code !== null) {
            $payload['error'] = [
                'code' => $code,
                'details' => $details,
            ];
        }

        return response()->json($payload, $status);
    }
}
