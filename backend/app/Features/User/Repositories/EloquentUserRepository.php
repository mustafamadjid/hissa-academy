<?php

namespace App\Features\User\Repositories;

use App\Features\User\Contracts\UserRepositoryContract;
use App\Features\User\Models\User;

final class EloquentUserRepository implements UserRepositoryContract
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
