<?php

use App\Features\Auth\Http\Controllers\GoogleAuthController;
use App\Features\Auth\Http\Controllers\UserAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->middleware('web')->group(function (): void {
    Route::post('/login', [UserAuthController::class, 'store'])
        ->name('auth.login')
        ->middleware('throttle:login');

    Route::post('/logout', [UserAuthController::class, 'destroy'])
        ->name('auth.logout');

    Route::get('/google/redirect', [GoogleAuthController::class, 'redirect'])
        ->name('auth.google.redirect');

    Route::get('/google/callback', [GoogleAuthController::class, 'callback'])
        ->name('auth.google.callback');

    Route::get('/me', [UserAuthController::class, 'me'])
        ->middleware(['auth:sanctum', 'throttle:api'])
        ->name('auth.me');
});
