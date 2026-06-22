<?php

namespace App\Providers\User;

use App\Features\User\Contracts\UserRepositoryContract;
use App\Features\User\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register any user feature services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryContract::class, EloquentUserRepository::class);
    }
}
