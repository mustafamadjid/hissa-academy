<?php

namespace App\Features\Quizz\Contracts;

use App\Features\Certificate\Models\Certificate;
use App\Features\Course\Models\Course;
use App\Features\Quizz\Models\Answer;
use App\Features\Quizz\Models\QuizAttempt;
use App\Features\Quizz\Models\Quizz;
use App\Features\User\Models\User;

interface StudentQuizRepositoryContract
{
    public function findActiveCourseQuiz(string $courseId): ?Quizz;

    public function findQuizForAttempt(string $quizId): ?Quizz;

    public function countAttempts(string $userId, string $quizId): int;

    public function findActiveAttempt(string $userId, string $quizId): ?QuizAttempt;

    public function createAttempt(User $user, Quizz $quiz): QuizAttempt;

    public function findAttemptForUser(string $attemptId, string $userId): ?QuizAttempt;

    public function findAnswerForQuestion(string $answerId, string $questionId): ?Answer;

    /**
     * @param  array<int, array{question_id: string, answer_option_id: string, is_correct: bool}>  $answers
     */
    public function submitAttempt(QuizAttempt $attempt, int $score, string $status, array $answers): QuizAttempt;

    public function findIssuedCertificate(string $userId, string $courseId): ?Certificate;

    public function createCertificate(User $user, Course $course, string $pdfPath): Certificate;
}
