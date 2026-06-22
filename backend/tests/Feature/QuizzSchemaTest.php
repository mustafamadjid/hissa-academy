<?php

use App\Features\Course\Models\Course;
use App\Features\Quizz\Models\Quizz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('creates quizzs with the expected schema and factory defaults', function () {
    expect(Schema::hasColumns('quizzs', [
        'id',
        'course_id',
        'quiz_name',
        'is_active',
        'created_at',
        'updated_at',
    ]))->toBeTrue();

    $quizz = Quizz::factory()->create();

    expect($quizz->id)->toBeString()
        ->and($quizz->course)->toBeInstanceOf(Course::class)
        ->and($quizz->quiz_name)->not->toBeEmpty()
        ->and($quizz->is_active)->toBeBool();
});
