<?php

use App\Providers\AppServiceProvider;
use App\Providers\Course\CourseServiceProvider;
use App\Providers\Lesson\LessonServiceProvider;
use App\Providers\LessonVideo\LessonVideoServiceProvider;
use App\Providers\User\UserServiceProvider;

return [
    AppServiceProvider::class,
    CourseServiceProvider::class,
    LessonServiceProvider::class,
    LessonVideoServiceProvider::class,
    UserServiceProvider::class,
];
