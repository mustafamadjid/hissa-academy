<?php

use App\Features\User\Enums\UserRole;
use Illuminate\Support\Facades\Route;

it('protects every admin api route with sanctum admin role and throttling middleware', function () {
    $adminRoutes = collect(Route::getRoutes())
        ->filter(fn ($route) => str_starts_with((string) $route->getName(), 'api.v1.admin.'));

    expect($adminRoutes)->not->toBeEmpty();

    $adminRoutes->each(function ($route) {
        expect($route->gatherMiddleware())
            ->toContain('auth:sanctum')
            ->toContain('role:' . UserRole::ADMIN->value)
            ->toContain('throttle:api');
    });
});
