<?php

use App\Features\Course\Http\Resources\CourseResource;
use App\Features\Course\Models\Course;
use App\Features\Lesson\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('maps a course model to the public API shape', function () {
    $course = Course::factory()->create([
        'course_name' => 'Laravel Basics',
        'description' => 'Introductory Laravel course',
        'minimum_score' => 70,
        'status' => 'active',
    ]);

    $resource = new CourseResource($course);

    expect($resource->resolve(Request::create('/')))->toBe([
        'id' => $course->id,
        'name' => 'Laravel Basics',
        'description' => 'Introductory Laravel course',
        'minimum_score' => 70,
        'status' => 'active',
    ]);
});

it('includes ordered lessons and their total when they are loaded', function () {
    $course = Course::factory()->create();
    $secondLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'position' => 2,
    ]);
    $firstLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'position' => 1,
    ]);
    $course->load([
        'lessons' => fn ($query) => $query->orderBy('position'),
    ])->loadCount('lessons');

    $resource = new CourseResource($course);
    $resolved = $resource->resolve(Request::create('/'));

    expect($resolved['total_lessons'])->toBe(2)
        ->and($resolved['lessons'])->toHaveCount(2)
        ->and($resolved['lessons'][0]['id'])->toBe($firstLesson->id)
        ->and($resolved['lessons'][1]['id'])->toBe($secondLesson->id);
});
