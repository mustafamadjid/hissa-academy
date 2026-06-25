<?php

namespace App\Providers\Student;

use App\Features\Student\Contracts\StudentRepositoryContract;
use App\Features\Student\Repositories\EloquentStudentRepository;
use Illuminate\Support\ServiceProvider;

final class StudentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StudentRepositoryContract::class, EloquentStudentRepository::class);
    }
}
