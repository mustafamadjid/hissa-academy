<?php

namespace App\Features\User\DTOs;

use App\Features\User\Models\User;

final readonly class UserProfileData
{
    public function __construct(
        public string $email,
        public string $fullName,
        public ?string $avatarUrl,
        public string $role,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            email: $user->email,
            fullName: $user->full_name,
            avatarUrl: $user->avatar_url,
            role: $user->role->name,
        );
    }
}
