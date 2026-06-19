<?php

namespace App\Features\Auth\DTOs;

use Laravel\Socialite\Contracts\User as SocialiteUser;

final readonly class GoogleUserData
{
    public function __construct(
        public string $googleId,
        public string $email,
        public string $fullName,
        public ?string $avatarUrl,
        public bool $emailVerified,
    ) {}

    public static function fromSocialite(SocialiteUser $user): self
    {
        $email = $user->getEmail();
        if ($email === null) {
            throw new \InvalidArgumentException('Google user has no email');
        }

        $fullname = $user->getName() ?? $user->getNickname();

        return new self(
            googleId: (string) $user->getId(),
            email: (string) $email,
            fullName: (string) $fullname,
            avatarUrl: $user->getAvatar(),
            emailVerified: (bool) ($user->user['email_verified'] ?? false),
        );
    }

    /**
     * @return array<string, string|null>
     */
    public function toUserAttributes(): array
    {
        return [
            'google_id' => $this->googleId,
            'email' => $this->email,
            'full_name' => $this->fullName,
            'avatar_url' => $this->avatarUrl,
        ];
    }
}
