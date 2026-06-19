<?php

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonVideo;
use App\Models\Quizz;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProgress;
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
