<?php

namespace App\Features\Auth\DTOs;

final readonly class LoginData
{
    public function __construct(
        public readonly string $email,
        public readonly string $password
    ) {}

    public function credentials(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
