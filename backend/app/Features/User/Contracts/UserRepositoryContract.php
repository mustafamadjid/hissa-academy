<?php

namespace App\Features\User\Contracts;

use App\Features\User\Models\User;

interface UserRepositoryContract
{
    public function findByEmail(string $email): ?User;

    public function findProfileById(string $userId): ?User;
}
