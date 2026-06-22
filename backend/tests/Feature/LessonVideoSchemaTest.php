<?php

use App\Features\Lesson\Models\Lesson;
use App\Features\LessonVideo\Models\LessonVideo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('creates lesson videos with the expected schema and factory defaults', function () {
    expect(Schema::hasColumns('lesson_videos', [
        'id',
        'lesson_id',
        'video_url',
        'duration_seconds',
        'created_at',
        'updated_at',
    ]))->toBeTrue();

    $lessonVideo = LessonVideo::factory()->create();

    expect($lessonVideo->id)->toBeString()
        ->and($lessonVideo->lesson)->toBeInstanceOf(Lesson::class)
        ->and($lessonVideo->video_url)->not->toBeEmpty()
        ->and($lessonVideo->duration_seconds)->toBeInt();
});
