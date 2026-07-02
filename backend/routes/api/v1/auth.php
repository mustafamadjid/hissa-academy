<?php

use App\Features\Auth\Http\Controllers\GoogleAuthController;
use App\Features\Auth\Http\Controllers\UserAuthController;
use App\Features\User\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::prefix('auth')->group(function (): void {
    Route::post('/login', [UserAuthController::class, 'store'])
        ->name('auth.login')
        ->middleware('throttle:login');

    Route::post('/logout', [UserAuthController::class, 'destroy'])
        ->middleware(['auth:sanctum', 'throttle:api'])
        ->name('auth.logout');

    Route::middleware('web')
        ->withoutMiddleware(EnsureFrontendRequestsAreStateful::class)
        ->group(function (): void {
            Route::get('/google/redirect', [GoogleAuthController::class, 'redirect'])
                ->name('auth.google.redirect');

            Route::get('/google/callback', [GoogleAuthController::class, 'callback'])
                ->name('auth.google.callback');
        });

    Route::get('/me', [UserProfileController::class, 'show'])
        ->middleware(['auth:sanctum', 'throttle:api'])
        ->name('auth.me');
});
