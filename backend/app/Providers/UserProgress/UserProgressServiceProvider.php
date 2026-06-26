<?php

namespace App\Providers\UserProgress;

use App\Features\UserProgress\Contracts\UserProgressRepositoryContract;
use App\Features\UserProgress\Repositories\EloquentUserProgressRepository;
use Illuminate\Support\ServiceProvider;

final class UserProgressServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserProgressRepositoryContract::class, EloquentUserProgressRepository::class);
    }
}
