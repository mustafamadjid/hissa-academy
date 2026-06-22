<?php

use App\Features\Auth\Http\Controllers\GoogleAuthController;
use App\Features\Auth\Http\Controllers\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->middleware('web')->group(function (): void {
    Route::post('/login', [UserAuthController::class, 'store'])
        ->name('auth.login');

    Route::post('/logout', [UserAuthController::class, 'destroy'])
        ->name('auth.logout');

    Route::get('/google/redirect', [GoogleAuthController::class, 'redirect'])
        ->name('auth.google.redirect');

    Route::get('/google/callback', [GoogleAuthController::class, 'callback'])
        ->name('auth.google.callback');

    Route::get('/me', function (Request $request) {
        return $request->user()->load('role');
    })->middleware('auth:sanctum')
        ->name('auth.me');
});
