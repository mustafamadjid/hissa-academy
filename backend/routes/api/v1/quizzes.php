<?php

use App\Features\Quizz\Http\Controllers\AdminQuizController;
use App\Features\Quizz\Http\Controllers\CourseFinalQuizController;
use App\Features\Quizz\Http\Controllers\StudentQuizController;
use App\Features\User\Enums\UserRole;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:'.UserRole::STUDENT->value, 'throttle:api'])
    ->group(function (): void {
        Route::get('/courses/{course_uuid}/quiz', [StudentQuizController::class, 'courseQuiz'])
            ->name('courses.quiz.show');

        Route::post('/quizzes/{quiz_uuid}/attempts', [StudentQuizController::class, 'createAttempt'])
            ->name('quizzes.attempts.store');

        Route::get('/quiz-attempts/{attempt_uuid}', [StudentQuizController::class, 'attempt'])
            ->name('quiz-attempts.show');

        Route::post('/quiz-attempts/{attempt_uuid}/submit', [StudentQuizController::class, 'submitAttempt'])
            ->name('quiz-attempts.submit');
    });

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:sanctum', 'role:'.UserRole::ADMIN->value, 'throttle:api'])
    ->group(function (): void {
        Route::get('/courses/{course_uuid}/quiz', [CourseFinalQuizController::class, 'show'])
            ->name('courses.quiz.show');

        Route::post('/courses/{course_uuid}/quiz', [CourseFinalQuizController::class, 'store'])
            ->name('courses.quiz.store');

        Route::patch('/quizzes/{quiz_uuid}', [AdminQuizController::class, 'update'])
            ->name('quizzes.update');

        Route::delete('/quizzes/{quiz_uuid}', [AdminQuizController::class, 'destroy'])
            ->name('quizzes.destroy');

        Route::get('/quizzes/{quiz_uuid}/questions', [AdminQuizController::class, 'questions'])
            ->name('quizzes.questions.index');

        Route::post('/quizzes/{quiz_uuid}/questions', [AdminQuizController::class, 'storeQuestions'])
            ->name('quizzes.questions.store');

        Route::patch('/quizzes/{quiz_uuid}/questions/reorder', [AdminQuizController::class, 'reorderQuestions'])
            ->name('quizzes.questions.reorder');

        Route::patch('/quiz-questions/{question_uuid}', [AdminQuizController::class, 'updateQuestion'])
            ->name('quiz-questions.update');

        Route::delete('/quiz-questions/{question_uuid}', [AdminQuizController::class, 'destroyQuestion'])
            ->name('quiz-questions.destroy');
    });
