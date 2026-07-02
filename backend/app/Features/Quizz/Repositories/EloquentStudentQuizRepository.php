<?php

namespace App\Features\Quizz\Repositories;

use App\Features\Certificate\Models\Certificate;
use App\Features\Course\Models\Course;
use App\Features\Quizz\Contracts\StudentQuizRepositoryContract;
use App\Features\Quizz\Models\Answer;
use App\Features\Quizz\Models\QuizAttempt;
use App\Features\Quizz\Models\Quizz;
use App\Features\User\Models\User;
use Illuminate\Support\Facades\DB;

final class EloquentStudentQuizRepository implements StudentQuizRepositoryContract
{
    public function findActiveCourseQuiz(string $courseId): ?Quizz
    {
        return Quizz::query()
            ->with([
                'course.lessons' => fn ($query) => $query->where('is_required', true),
                'questions.answers',
            ])
            ->where('course_id', $courseId)
            ->where('is_active', true)
            ->first();
    }

    public function findQuizForAttempt(string $quizId): ?Quizz
    {
        return Quizz::query()
            ->with([
                'course.lessons' => fn ($query) => $query->where('is_required', true),
                'questions.answers',
            ])
            ->where('is_active', true)
            ->find($quizId);
    }

    public function countAttempts(string $userId, string $quizId): int
    {
        return QuizAttempt::query()
            ->where('user_id', $userId)
            ->where('quizz_id', $quizId)
            ->count();
    }

    public function findActiveAttempt(string $userId, string $quizId): ?QuizAttempt
    {
        return QuizAttempt::query()
            ->where('user_id', $userId)
            ->where('quizz_id', $quizId)
            ->where('status', 'in_progress')
            ->first();
    }

    public function createAttempt(User $user, Quizz $quiz): QuizAttempt
    {
        return QuizAttempt::query()
            ->create([
                'user_id' => $user->id,
                'quizz_id' => $quiz->id,
                'status' => 'in_progress',
                'score' => null,
                'started_at' => now(),
                'submitted_at' => null,
            ])
            ->load(['quiz.course', 'quiz.questions.answers', 'answers']);
    }

    public function findAttemptForUser(string $attemptId, string $userId): ?QuizAttempt
    {
        return QuizAttempt::query()
            ->with([
                'quiz.course.lessons' => fn ($query) => $query->where('is_required', true),
                'quiz.questions.answers',
                'answers',
            ])
            ->where('user_id', $userId)
            ->find($attemptId);
    }

    public function findAnswerForQuestion(string $answerId, string $questionId): ?Answer
    {
        return Answer::query()
            ->where('question_id', $questionId)
            ->find($answerId);
    }

    public function submitAttempt(QuizAttempt $attempt, int $score, string $status, array $answers): QuizAttempt
    {
        return DB::transaction(function () use ($attempt, $score, $status, $answers): QuizAttempt {
            $attempt->answers()->delete();
            $attempt->answers()->createMany($answers);
            $attempt->forceFill([
                'score' => $score,
                'status' => $status,
                'submitted_at' => now(),
            ])->save();

            return $attempt->refresh()->load(['quiz.course', 'quiz.questions.answers', 'answers']);
        });
    }

    public function findIssuedCertificate(string $userId, string $courseId): ?Certificate
    {
        return Certificate::query()
            ->with('course')
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('status', 'issued')
            ->first();
    }

    public function createCertificate(User $user, Course $course, string $pdfPath): Certificate
    {
        return Certificate::query()
            ->create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'issued_at' => now(),
                'status' => 'issued',
                'pdf_path' => $pdfPath,
            ])
            ->load('course');
    }
}
