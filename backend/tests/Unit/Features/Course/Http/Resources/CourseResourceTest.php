<?php

use App\Features\Course\Http\Resources\CourseResource;
use App\Features\Course\Models\Course;
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

    expect($resource->toArray(Request::create('/')))->toBe([
        'id' => $course->id,
        'name' => 'Laravel Basics',
        'description' => 'Introductory Laravel course',
        'minimum_score' => 70,
        'status' => 'active',
    ]);
});
