<?php

use App\Features\Certificate\Models\Certificate;
use App\Features\Course\Models\Course;
use App\Features\Lesson\Models\Lesson;
use App\Features\LessonVideo\Models\LessonVideo;
use App\Features\Quizz\Models\Quizz;
use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use App\Features\UserProgress\Models\UserProgress;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('uses soft deletes on all application models and tables', function () {
    $models = [
        Certificate::class,
        Course::class,
        Lesson::class,
        LessonVideo::class,
        Quizz::class,
        Role::class,
        User::class,
        UserProgress::class,
    ];

    foreach ($models as $model) {
        expect(class_uses_recursive($model))
            ->toContain(SoftDeletes::class);

        expect(Schema::hasColumn((new $model())->getTable(), 'deleted_at'))
            ->toBeTrue();
    }
});
