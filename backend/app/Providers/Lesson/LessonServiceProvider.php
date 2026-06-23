<?php

namespace App\Providers\Lesson;

use App\Features\Lesson\Contracts\LessonRepositoryContract;
use App\Features\Lesson\Repositories\EloquentLessonRepository;
use Illuminate\Support\ServiceProvider;

class LessonServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LessonRepositoryContract::class, EloquentLessonRepository::class);
    }
}
