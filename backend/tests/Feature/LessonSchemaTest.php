<?php

use App\Features\Course\Models\Course;
use App\Features\Lesson\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('creates lessons with the expected schema and factory defaults', function () {
    expect(Schema::hasColumns('lessons', [
        'id',
        'public_uuid',
        'course_id',
        'title',
        'position',
        'is_required',
        'created_at',
        'updated_at',
        'deleted_at',
    ]))->toBeTrue();

    $lesson = Lesson::factory()->create();

    expect($lesson->public_uuid)->not->toBeEmpty()
        ->and($lesson->course)->toBeInstanceOf(Course::class)
        ->and($lesson->title)->not->toBeEmpty()
        ->and($lesson->position)->toBeInt()
        ->and($lesson->is_required)->toBeBool();
});
