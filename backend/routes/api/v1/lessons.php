<?php

use App\Features\Lesson\Http\Controllers\LessonController;
use App\Features\LessonVideo\Http\Controllers\LessonVideoController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:sanctum', 'role:admin', 'throttle:api'])
    ->group(function (): void {
        Route::get('/courses/{course_uuid}/lessons', [LessonController::class, 'index'])
            ->name('courses.lessons.index');

        Route::post('/courses/{course_uuid}/lessons', [LessonController::class, 'store'])
            ->name('courses.lessons.store');

        Route::patch('/courses/{courseId}/lessons/reorder', [LessonController::class, 'reorder'])
            ->name('courses.lessons.reorder');

        Route::get('/lessons/{lesson_uuid}', [LessonController::class, 'show'])
            ->name('lessons.show');

        Route::patch('/lessons/{lesson_uuid}', [LessonController::class, 'update'])
            ->name('lessons.update');

        Route::put('/lessons/{lesson_uuid}/video', [LessonVideoController::class, 'update'])
            ->name('lessons.video.update');

        Route::delete('/lessons/{lesson_uuid}/video', [LessonVideoController::class, 'destroy'])
            ->name('lessons.video.destroy');

        Route::delete('/lessons/{lesson_uuid}', [LessonController::class, 'destroy'])
            ->name('lessons.destroy');
    });
