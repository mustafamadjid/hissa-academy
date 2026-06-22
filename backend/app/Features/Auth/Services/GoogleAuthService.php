<?php

namespace App\Features\Auth\Services;

use App\Features\Auth\DTOs\GoogleUserData;
use App\Features\User\Enums\UserRole;
use App\Features\User\Models\Role;
use App\Features\User\Models\User;

class GoogleAuthService
{
    public function loginOrRegister(GoogleUserData $googleUser): User
    {
        $user = User::where('google_id', $googleUser->googleId)->first();
        if ($user === null) {
            $user = User::where('email', $googleUser->email)->first();
        }

        if ($user !== null) {
            $user->update([
                'google_id' => $googleUser->googleId,
                'full_name' => $googleUser->fullName,
                'avatar_url' => $googleUser->avatarUrl,
                'email_verified_at' => $googleUser->emailVerified
                    ? now()
                    : null,
                'last_login_at' => now(),
            ]);

            return $user;
        }

        return User::create([
            'google_id' => $googleUser->googleId,
            'email' => $googleUser->email,
            'full_name' => $googleUser->fullName,
            'avatar_url' => $googleUser->avatarUrl,
            'role_id' => $this->defaultRoleId(),
            'email_verified_at' => $googleUser->emailVerified ? now() : null,
            'last_login_at' => now(),
        ]);
    }

    private function defaultRoleId(): int
    {
        return Role::where('name', UserRole::STUDENT->value)->value('id');
    }
}
