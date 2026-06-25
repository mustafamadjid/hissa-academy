<?php

namespace App\Features\Quizz\Services;

use App\Features\Quizz\Contracts\QuizzRepositoryContract;
use App\Features\Quizz\DTOs\QuestionCreateData;
use App\Features\Quizz\DTOs\QuizzCreateData;
use App\Features\Quizz\Exceptions\QuizzOperationException;
use App\Features\Quizz\Models\Quizz;
use App\Features\User\Models\User;
use App\Helper\EnsureAdminForService;
use Illuminate\Support\Collection;
use Throwable;

final class QuizzService
{
    public function __construct(
        private readonly QuizzRepositoryContract $quizzRepository,
        private readonly EnsureAdminForService $ensureAdmin,
    ) {}

    public function findFinalQuizByCourse(string $courseId): ?Quizz
    {
        try {
            $course = $this->quizzRepository->findCourseById($courseId);

            if ($course === null) {
                return null;
            }

            return $this->quizzRepository->findFinalQuizByCourse($course);
        } catch (Throwable $exception) {
            throw new QuizzOperationException('Gagal mengambil konfigurasi quiz.', $exception);
        }
    }

    public function courseExists(string $courseId): bool
    {
        try {
            return $this->quizzRepository->findCourseById($courseId) !== null;
        } catch (Throwable $exception) {
            throw new QuizzOperationException('Gagal mengambil detail course.', $exception);
        }
    }

    public function createFinalQuiz(string $courseId, QuizzCreateData $data, ?User $actor): ?Quizz
    {
        $this->ensureAdmin->ensureAdmin($actor);

        try {
            $course = $this->quizzRepository->findCourseById($courseId);

            if ($course === null) {
                return null;
            }

            return $this->quizzRepository->createFinalQuiz($course, $data);
        } catch (Throwable $exception) {
            throw new QuizzOperationException('Gagal membuat quiz.', $exception);
        }
    }

    public function updateQuiz(string $quizId, QuizzCreateData $data, ?User $actor): ?Quizz
    {
        $this->ensureAdmin->ensureAdmin($actor);

        try {
            $quiz = $this->quizzRepository->findQuizById($quizId);

            if ($quiz === null) {
                return null;
            }

            return $this->quizzRepository->updateQuiz($quiz, $data);
        } catch (Throwable $exception) {
            throw new QuizzOperationException('Gagal memperbarui quiz.', $exception);
        }
    }

    public function deleteQuiz(string $quizId, ?User $actor): bool
    {
        $this->ensureAdmin->ensureAdmin($actor);

        try {
            $quiz = $this->quizzRepository->findQuizById($quizId);

            if ($quiz === null) {
                return false;
            }

            return $this->quizzRepository->deleteQuiz($quiz);
        } catch (Throwable $exception) {
            throw new QuizzOperationException('Gagal menghapus quiz.', $exception);
        }
    }

    public function listQuestions(string $quizId, ?User $actor): ?Collection
    {
        $this->ensureAdmin->ensureAdmin($actor);

        try {
            $quiz = $this->quizzRepository->findQuizById($quizId);

            if ($quiz === null) {
                return null;
            }

            return $this->quizzRepository->listQuestionsWithAnswers($quiz);
        } catch (Throwable $exception) {
            throw new QuizzOperationException('Gagal mengambil pertanyaan quiz.', $exception);
        }
    }

    /**
     * @param  array<int, QuestionCreateData>  $questions
     */
    public function createQuestions(string $quizId, array $questions, ?User $actor): ?Collection
    {
        $this->ensureAdmin->ensureAdmin($actor);

        try {
            $quiz = $this->quizzRepository->findQuizById($quizId);

            if ($quiz === null) {
                return null;
            }

            return $this->quizzRepository->createQuestionsWithAnswers($quiz, $questions);
        } catch (Throwable $exception) {
            throw new QuizzOperationException('Gagal membuat pertanyaan quiz.', $exception);
        }
    }
}
