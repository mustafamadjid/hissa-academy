<?php

use App\Features\Quizz\Http\Controllers\AdminQuizController;
use App\Features\Quizz\Http\Controllers\CourseFinalQuizController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware('throttle:api')
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

        Route::patch('/quiz-questions/{question_uuid}', [AdminQuizController::class, 'updateQuestion'])
            ->name('quiz-questions.update');
    });
