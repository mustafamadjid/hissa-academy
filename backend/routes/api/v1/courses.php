<?php

use App\Features\Course\Http\Controllers\CourseController;
use App\Features\User\Enums\UserRole;
use Illuminate\Support\Facades\Route;

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
