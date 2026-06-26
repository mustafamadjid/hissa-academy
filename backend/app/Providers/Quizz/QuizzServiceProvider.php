<?php

namespace App\Providers\Quizz;

use App\Features\Quizz\Contracts\QuizzRepositoryContract;
use App\Features\Quizz\Contracts\StudentQuizRepositoryContract;
use App\Features\Quizz\Repositories\EloquentQuizzRepository;
use App\Features\Quizz\Repositories\EloquentStudentQuizRepository;
use Illuminate\Support\ServiceProvider;

class QuizzServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(QuizzRepositoryContract::class, EloquentQuizzRepository::class);
        $this->app->bind(StudentQuizRepositoryContract::class, EloquentStudentQuizRepository::class);
    }
}
