<?php

namespace App\Providers\Course;

use App\Features\Course\Contracts\CourseRepositoryContract;
use App\Features\Course\Repositories\EloquentCourseRepository;
use Illuminate\Support\ServiceProvider;

class CourseServiceProvider extends ServiceProvider
{
    /**
     * Register any course feature services.
     */
    public function register(): void
    {
        $this->app->bind(CourseRepositoryContract::class, EloquentCourseRepository::class);
    }
}
