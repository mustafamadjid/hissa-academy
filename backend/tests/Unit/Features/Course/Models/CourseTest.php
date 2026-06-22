<?php

use App\Features\Course\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('creates courses with uuid primary keys from the factory', function () {
    $course = Course::factory()->create();

    expect($course->id)->not->toBeNull()
        ->and($course->getKeyType())->toBe('string')
        ->and($course->getIncrementing())->toBeFalse();
});

it('soft deletes courses', function () {
    $course = Course::factory()->create();

    $course->delete();

    $this->assertSoftDeleted('courses', [
        'id' => $course->id,
    ]);
});
