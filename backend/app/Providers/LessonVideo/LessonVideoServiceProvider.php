<?php

namespace App\Providers\LessonVideo;

use App\Features\LessonVideo\Contracts\LessonVideoRepositoryContract;
use App\Features\LessonVideo\Repositories\EloquentLessonVideoRepository;
use Illuminate\Support\ServiceProvider;

final class LessonVideoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LessonVideoRepositoryContract::class, EloquentLessonVideoRepository::class);
    }
}
