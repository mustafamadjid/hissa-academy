<?php

namespace App\Providers\Quizz;

use App\Features\Quizz\Contracts\QuizzRepositoryContract;
use App\Features\Quizz\Repositories\EloquentQuizzRepository;
use Illuminate\Support\ServiceProvider;

class QuizzServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(QuizzRepositoryContract::class, EloquentQuizzRepository::class);
    }
}
