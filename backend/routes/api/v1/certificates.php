<?php

use App\Features\Certificate\Http\Controllers\CertificateController;
use App\Features\User\Enums\UserRole;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/certificates')
    ->name('admin.certificates.')
    ->middleware(['auth:sanctum', 'role:' . UserRole::ADMIN->value, 'throttle:api'])
    ->group(function (): void {
        Route::get('/', [CertificateController::class, 'index'])
            ->name('index');

        Route::patch('/{certificate_uuid}/revoke', [CertificateController::class, 'revoke'])
            ->name('revoke');
    });
