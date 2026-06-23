<?php

use App\Providers\AppServiceProvider;
use App\Providers\Course\CourseServiceProvider;
use App\Providers\Lesson\LessonServiceProvider;
use App\Providers\User\UserServiceProvider;

return [
    AppServiceProvider::class,
    CourseServiceProvider::class,
    LessonServiceProvider::class,
    UserServiceProvider::class,
];
