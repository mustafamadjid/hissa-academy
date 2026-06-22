<?php

namespace App\Features\Auth\Services;

use App\Features\Auth\DTOs\LoginData;
use App\Features\Auth\Exceptions\InvalidCredentialsException;
use App\Features\User\Contracts\UserRepositoryContract;
use App\Features\User\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

final class UserAuthService
{
    public function __construct(
        private readonly UserRepositoryContract $userRepository,
    ) {}

    public function login(LoginData $loginData): User
    {
        $user = $this->userRepository->findByEmail($loginData->email);

        if (
            $user === null ||
            ! Hash::check($loginData->password, $user->password)
        ) {
            $this->logFailedLogin($loginData);

            throw new InvalidCredentialsException;
        }

        Auth::guard('web')->login(
            user: $user,
        );

        $user->update([
            'last_login_at' => now(),
        ]);

        $this->logSuccessfulLogin($loginData);

        return $user;
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
    }

    private function logFailedLogin(LoginData $loginData): void
    {
        Log::warning('Login attempt failed', [
            'event' => 'auth.login.failed',
            'email_hash' => hash(
                'sha256',
                strtolower($loginData->email),
            ),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'occurred_at' => now()->toISOString(),
        ]);
    }

    private function logSuccessfulLogin(LoginData $loginData): void
    {
        Log::info('Login succeeded', [
            'event' => 'auth.login.succeeded',
            'email_hash' => hash(
                'sha256',
                strtolower($loginData->email),
            ),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'occurred_at' => now()->toISOString(),
        ]);
    }
}
