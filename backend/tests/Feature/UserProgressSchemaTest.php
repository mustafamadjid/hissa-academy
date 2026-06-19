<?php

use App\Models\Lesson;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('creates user progress with the expected schema and factory defaults', function () {
    expect(Schema::hasColumns('user_progress', [
        'id',
        'user_id',
        'lesson_id',
        'last_position_seconds',
        'total_watched_seconds',
        'status',
        'completed_at',
        'created_at',
        'updated_at',
    ]))->toBeTrue();

    $userProgress = UserProgress::factory()->create();

    expect($userProgress->user)->toBeInstanceOf(User::class)
        ->and($userProgress->lesson)->toBeInstanceOf(Lesson::class)
        ->and($userProgress->last_position_seconds)->toBeInt()
        ->and($userProgress->total_watched_seconds)->toBeInt()
        ->and($userProgress->status)->not->toBeEmpty();
});
