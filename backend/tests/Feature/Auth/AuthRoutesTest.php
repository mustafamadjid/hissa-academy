<?php

use App\Features\User\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

it('routes user login requests to validation instead of returning not found', function () {
    $response = $this->postJson('/api/v1/auth/login');

    $response->assertStatus(422);
});

it('routes authenticated user profile requests to the auth controller', function () {
    $route = Route::getRoutes()->getByName('api.v1.auth.me');

    expect($route?->getActionName())->toBe(UserProfileController::class.'@show');
});

it('protects logout requests with Sanctum authentication', function () {
    $this->postJson('/api/v1/auth/logout')->assertUnauthorized();
});
