<?php

use App\Features\Auth\DTOs\LoginData;
use App\Features\Auth\Exceptions\InvalidCredentialsException;
use App\Features\Auth\Services\UserAuthService;
use App\Features\User\Contracts\UserRepositoryContract;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('logs in a user and updates the last login timestamp when credentials are valid', function () {
    $this->travelTo(now()->startOfSecond());
    Log::spy();

    $user = User::factory()->create([
        'email' => 'student@example.com',
        'password' => Hash::make('correct-password'),
        'last_login_at' => null,
    ]);

    $repository = Mockery::mock(UserRepositoryContract::class);
    $repository->shouldReceive('findByEmail')
        ->once()
        ->with('student@example.com')
        ->andReturn($user);

    $service = new UserAuthService($repository);

    $loggedInUser = $service->login(new LoginData(
        email: 'student@example.com',
        password: 'correct-password',
    ));

    expect($loggedInUser->is($user))->toBeTrue()
        ->and(Auth::guard('web')->id())->toBe($user->id)
        ->and($user->fresh()->last_login_at?->equalTo(now()))->toBeTrue();

    Log::shouldHaveReceived('info')
        ->once()
        ->with('Login succeeded', Mockery::on(
            fn (array $context): bool => $context['event'] === 'auth.login.succeeded'
                && $context['email_hash'] === hash('sha256', 'student@example.com')
        ));
});

it('rejects an invalid password without logging the user in', function () {
    Log::spy();

    $user = User::factory()->create([
        'email' => 'student@example.com',
        'password' => Hash::make('correct-password'),
        'last_login_at' => null,
    ]);

    $repository = Mockery::mock(UserRepositoryContract::class);
    $repository->shouldReceive('findByEmail')
        ->once()
        ->with('student@example.com')
        ->andReturn($user);

    $service = new UserAuthService($repository);

    expect(fn () => $service->login(new LoginData(
        email: 'student@example.com',
        password: 'wrong-password',
    )))->toThrow(InvalidCredentialsException::class);

    expect(Auth::guard('web')->check())->toBeFalse()
        ->and($user->fresh()->last_login_at)->toBeNull();

    Log::shouldHaveReceived('warning')
        ->once()
        ->with('Login attempt failed', Mockery::on(
            fn (array $context): bool => $context['event'] === 'auth.login.failed'
                && $context['email_hash'] === hash('sha256', 'student@example.com')
        ));
});

it('logs out the current web guard user', function () {
    $user = User::factory()->create();
    Auth::guard('web')->login($user);

    $repository = Mockery::mock(UserRepositoryContract::class);
    $service = new UserAuthService($repository);

    $service->logout();

    expect(Auth::guard('web')->check())->toBeFalse();
});
