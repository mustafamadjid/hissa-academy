<?php

use App\Features\Certificate\Http\Controllers\CertificateController;
use App\Features\Certificate\Http\Controllers\StudentCertificateController;
use App\Features\User\Enums\UserRole;
use Illuminate\Support\Facades\Route;

Route::prefix('certificates')
    ->name('certificates.')
    ->middleware(['auth:sanctum', 'role:' . UserRole::STUDENT->value, 'throttle:api'])
    ->group(function (): void {
        Route::get('/', [StudentCertificateController::class, 'index'])
            ->name('index');

        Route::get('/{certificate_uuid}', [StudentCertificateController::class, 'show'])
            ->name('show');

        Route::get('/{certificate_uuid}/download', [StudentCertificateController::class, 'download'])
            ->name('download');
    });

Route::prefix('admin/certificates')
    ->name('admin.certificates.')
    ->middleware(['auth:sanctum', 'role:' . UserRole::ADMIN->value, 'throttle:api'])
    ->group(function (): void {
        Route::get('/', [CertificateController::class, 'index'])
            ->name('index');

        Route::patch('/{certificate_uuid}/revoke', [CertificateController::class, 'revoke'])
            ->name('revoke');
    });
