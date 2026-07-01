<?php

use App\Features\Course\Http\Controllers\CourseController;
use App\Features\Course\Http\Controllers\StudentCourseController;
use App\Features\User\Enums\UserRole;
use Illuminate\Support\Facades\Route;

Route::get('courses', [StudentCourseController::class, 'index'])
    ->middleware('throttle:api')
    ->name('courses.index');

Route::prefix('courses')
    ->name('courses.')
    ->middleware(['auth:sanctum', 'role:' . UserRole::STUDENT->value, 'throttle:api'])
    ->group(function (): void {
        Route::get('/{course_uuid}/progress', [StudentCourseController::class, 'progress'])
            ->name('progress');

        Route::get('/{course_uuid}', [StudentCourseController::class, 'show'])
            ->name('show');
    });

Route::prefix('admin/courses')
    ->name('admin.courses.')
    ->middleware(['auth:sanctum', 'role:' . UserRole::ADMIN->value, 'throttle:api'])
    ->group(function (): void {
        Route::get('/', [CourseController::class, 'index'])
            ->name('index');

        Route::post('/', [CourseController::class, 'store'])
            ->name('store');

        Route::get('/{course_uuid}', [CourseController::class, 'show'])
            ->name('show');

        Route::patch('/{course_uuid}', [CourseController::class, 'update'])
            ->name('update');

        Route::delete('/{course_uuid}', [CourseController::class, 'destroy'])
            ->name('destroy');
    });
