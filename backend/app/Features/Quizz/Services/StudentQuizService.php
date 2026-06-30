<?php

namespace App\Features\Quizz\Services;

use App\Features\Certificate\Models\Certificate;
use App\Features\Quizz\Contracts\StudentQuizRepositoryContract;
use App\Features\Quizz\DTOs\SubmitQuizAttemptData;
use App\Features\Quizz\Exceptions\StudentQuizOperationException;
use App\Features\Quizz\Models\Question;
use App\Features\Quizz\Models\QuizAttempt;
use App\Features\Quizz\Models\Quizz;
use App\Features\User\Models\User;
use App\Features\UserProgress\Contracts\UserProgressRepositoryContract;
use App\Helper\EnsureStudentForService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class StudentQuizService
{
    public function __construct(
        private readonly StudentQuizRepositoryContract $quizRepository,
        private readonly UserProgressRepositoryContract $progressRepository,
        private readonly EnsureStudentForService $ensureStudent,
    ) {}

    /**
     * @return array{quiz: Quizz, attempts_used: int}|null
     */
    public function courseQuiz(string $courseId, ?User $actor): ?array
    {
        $this->ensureStudent->ensureStudent($actor);

        try {
            $quiz = $this->quizRepository->findActiveCourseQuiz($courseId);

            if ($quiz === null) {
                return null;
            }

            $this->ensureQuizUnlocked($quiz, $actor);

            return [
                'quiz' => $quiz,
                'attempts_used' => $this->quizRepository->countAttempts($actor->id, $quiz->id),
            ];
        } catch (StudentQuizOperationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Gagal mengambil quiz.', [
                'course_id' => $courseId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new StudentQuizOperationException('Gagal mengambil quiz.', previous: $exception);
        }
    }

    public function createAttempt(string $quizId, ?User $actor): ?QuizAttempt
    {
        $this->ensureStudent->ensureStudent($actor);

        try {
            $quiz = $this->quizRepository->findQuizForAttempt($quizId);

            if ($quiz === null) {
                return null;
            }

            $this->ensureQuizUnlocked($quiz, $actor);

            $activeAttempt = $this->quizRepository->findActiveAttempt($actor->id, $quiz->id);

            if ($activeAttempt !== null) {
                throw new StudentQuizOperationException(
                    'Masih terdapat quiz attempt yang aktif.',
                    409,
                    'ACTIVE_ATTEMPT_EXISTS',
                    ['attempt_uuid' => $activeAttempt->id],
                );
            }

            return $this->quizRepository->createAttempt($actor, $quiz);
        } catch (StudentQuizOperationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Gagal membuat quiz attempt.', [
                'quiz_id' => $quizId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new StudentQuizOperationException('Gagal membuat quiz attempt.', previous: $exception);
        }
    }

    public function attempt(string $attemptId, ?User $actor): ?QuizAttempt
    {
        $this->ensureStudent->ensureStudent($actor);

        try {
            return $this->quizRepository->findAttemptForUser($attemptId, $actor->id);
        } catch (Throwable $exception) {
            Log::error('Gagal mengambil quiz attempt.', [
                'attempt_id' => $attemptId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new StudentQuizOperationException('Gagal mengambil quiz attempt.', previous: $exception);
        }
    }

    /**
     * @return array{
     *     attempt: QuizAttempt,
     *     correct_answers: int,
     *     incorrect_answers: int,
     *     total_questions: int,
     *     certificate: Certificate|null
     * }|null
     */
    public function submitAttempt(string $attemptId, SubmitQuizAttemptData $data, ?User $actor): ?array
    {
        $this->ensureStudent->ensureStudent($actor);

        try {
            $attempt = $this->quizRepository->findAttemptForUser($attemptId, $actor->id);

            if ($attempt === null) {
                return null;
            }

            if ($attempt->status !== 'in_progress') {
                throw new StudentQuizOperationException(
                    'Quiz attempt sudah pernah disubmit.',
                    409,
                    'ATTEMPT_ALREADY_SUBMITTED',
                );
            }

            $questions = $attempt->quiz->questions->keyBy('id');
            $selectedByQuestion = collect($data->answers)->keyBy('questionUuid');
            $totalPoints = max(1, (int) $questions->sum(fn (Question $question): int => (int) $question->points));
            $correctPoints = 0;
            $correctAnswers = 0;
            $answerRows = [];

            foreach ($questions as $question) {
                $submittedAnswer = $selectedByQuestion->get($question->id);
                $answer = $submittedAnswer === null
                    ? null
                    : $this->quizRepository->findAnswerForQuestion($submittedAnswer->selectedOptionUuid, $question->id);

                $isCorrect = (bool) $answer?->is_correct;

                if ($isCorrect) {
                    $correctAnswers++;
                    $correctPoints += (int) $question->points;
                }

                if ($answer !== null) {
                    $answerRows[] = [
                        'question_id' => $question->id,
                        'answer_option_id' => $answer->id,
                        'is_correct' => $isCorrect,
                    ];
                }
            }

            $score = (int) round(($correctPoints / $totalPoints) * 100);
            $minimumScore = (int) $attempt->quiz->course->minimum_score;
            $status = $score >= $minimumScore ? 'passed' : 'failed';
            $submittedAttempt = $this->quizRepository->submitAttempt($attempt, $score, $status, $answerRows);
            $certificate = $status === 'passed' ? $this->issueCertificate($actor, $submittedAttempt) : null;
            $totalQuestions = $questions->count();

            return [
                'attempt' => $submittedAttempt,
                'correct_answers' => $correctAnswers,
                'incorrect_answers' => $totalQuestions - $correctAnswers,
                'total_questions' => $totalQuestions,
                'certificate' => $certificate,
            ];
        } catch (StudentQuizOperationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Gagal submit quiz attempt.', [
                'attempt_id' => $attemptId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new StudentQuizOperationException('Gagal submit quiz attempt.', previous: $exception);
        }
    }

    private function ensureQuizUnlocked(Quizz $quiz, User $actor): void
    {
        $requiredLessonIds = $quiz->course?->lessons?->pluck('id')->all() ?? [];

        if ($requiredLessonIds === []) {
            return;
        }

        $completedRequiredLessonCount = $this->progressRepository
            ->forUserLessons($actor->id, $requiredLessonIds)
            ->filter(fn ($progress): bool => $progress->status === 'completed')
            ->count();

        if ($completedRequiredLessonCount !== count($requiredLessonIds)) {
            throw new StudentQuizOperationException(
                'Quiz belum dapat diakses.',
                403,
                'QUIZ_LOCKED',
                ['reason' => 'REQUIRED_LESSONS_NOT_COMPLETED'],
            );
        }
    }

    private function issueCertificate(User $actor, QuizAttempt $attempt): Certificate
    {
        $course = $attempt->quiz->course;
        $existingCertificate = $this->quizRepository->findIssuedCertificate($actor->id, $course->id);

        if ($existingCertificate !== null) {
            return $existingCertificate;
        }

        $certificate = $this->quizRepository->createCertificate(
            $actor,
            $course,
            'certificates/'.strtolower($attempt->id).'.pdf',
        );

        Storage::disk('local')->put(
            $certificate->pdf_path,
            "%PDF-1.4\n% HISSA certificate {$certificate->certificate_number}\n",
        );

        return $certificate;
    }
}
