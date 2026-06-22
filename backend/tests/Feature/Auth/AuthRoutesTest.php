<?php

use App\Features\Auth\Http\Controllers\UserAuthController;
use Illuminate\Support\Facades\Route;

it('routes user login requests to validation instead of returning not found', function () {
    $response = $this->postJson('/api/v1/auth/login');

    $response->assertStatus(422);
});

it('routes authenticated user profile requests to the auth controller', function () {
    $route = Route::getRoutes()->getByName('api.v1.auth.me');

    expect($route?->getActionName())->toBe(UserAuthController::class.'@me');
});
