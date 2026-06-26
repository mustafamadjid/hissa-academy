<?php

use App\Features\Student\Http\Controllers\AdminStudentController;
use App\Features\User\Enums\UserRole;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/students')
    ->name('admin.students.')
    ->middleware(['auth:sanctum', 'role:' . UserRole::ADMIN->value, 'throttle:api'])
    ->group(function (): void {
        Route::get('/', [AdminStudentController::class, 'index'])
            ->name('index');

        Route::get('/{student_uuid}', [AdminStudentController::class, 'show'])
            ->name('show');

        Route::get('/{student_uuid}/progress', [AdminStudentController::class, 'progress'])
            ->name('progress');

        Route::get('/{student_uuid}/certificates', [AdminStudentController::class, 'certificates'])
            ->name('certificates');
    });
