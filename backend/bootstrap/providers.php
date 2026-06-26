<?php

use App\Providers\AppServiceProvider;
use App\Providers\Certificate\CertificateServiceProvider;
use App\Providers\Course\CourseServiceProvider;
use App\Providers\Lesson\LessonServiceProvider;
use App\Providers\LessonVideo\LessonVideoServiceProvider;
use App\Providers\Quizz\QuizzServiceProvider;
use App\Providers\Student\StudentServiceProvider;
use App\Providers\User\UserServiceProvider;
use App\Providers\UserProgress\UserProgressServiceProvider;

return [
    AppServiceProvider::class,
    CertificateServiceProvider::class,
    CourseServiceProvider::class,
    LessonServiceProvider::class,
    LessonVideoServiceProvider::class,
    QuizzServiceProvider::class,
    StudentServiceProvider::class,
    UserServiceProvider::class,
    UserProgressServiceProvider::class,
];
