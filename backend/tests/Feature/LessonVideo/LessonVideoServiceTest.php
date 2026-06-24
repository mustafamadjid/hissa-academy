<?php

use App\Features\LessonVideo\DTOs\LessonVideoData;
use App\Features\LessonVideo\Exceptions\LessonVideoOperationException;
use App\Features\LessonVideo\Services\LessonVideoService;
use Illuminate\Support\Facades\Http;

it('returns mapped youtube video metadata', function () {
    config(['services.youtube.api_key' => 'test-api-key']);

    Http::fake([
        'https://www.googleapis.com/youtube/v3/videos*' => Http::response([
            'items' => [[
                'id' => 'video-123',
                'snippet' => [
                    'title' => 'Laravel Lesson',
                    'description' => 'Learn Laravel basics.',
                    'channelTitle' => 'HISSA',
                    'thumbnails' => [
                        'medium' => ['url' => 'https://img.youtube.com/medium.jpg'],
                        'default' => ['url' => 'https://img.youtube.com/default.jpg'],
                    ],
                ],
                'contentDetails' => [
                    'duration' => 'PT1H2M3S',
                ],
                'status' => [
                    'privacyStatus' => 'public',
                ],
            ]],
        ]),
    ]);

    $metadata = (new LessonVideoService)->getVideoMetadata('video-123');

    expect($metadata)->toBeInstanceOf(LessonVideoData::class)
        ->and($metadata->videoId)->toBe('video-123')
        ->and($metadata->title)->toBe('Laravel Lesson')
        ->and($metadata->description)->toBe('Learn Laravel basics.')
        ->and($metadata->channelTitle)->toBe('HISSA')
        ->and($metadata->thumbnailUrl)->toBe('https://img.youtube.com/medium.jpg')
        ->and($metadata->durationIso)->toBe('PT1H2M3S')
        ->and($metadata->durationSeconds)->toBe(3723)
        ->and($metadata->privacyStatus)->toBe('public');

    Http::assertSent(fn ($request) => str_starts_with($request->url(), 'https://www.googleapis.com/youtube/v3/videos')
        && $request['part'] === 'snippet,contentDetails,status'
        && $request['id'] === 'video-123'
        && $request['key'] === 'test-api-key');
});

it('throws a custom exception when youtube request fails', function () {
    Http::fake([
        'https://www.googleapis.com/youtube/v3/videos*' => Http::response([], 500),
    ]);

    expect(fn () => (new LessonVideoService)->getVideoMetadata('video-123'))
        ->toThrow(LessonVideoOperationException::class, 'Gagal mengambil metadata video.');
});

it('throws a custom exception when youtube video is not found', function () {
    Http::fake([
        'https://www.googleapis.com/youtube/v3/videos*' => Http::response(['items' => []]),
    ]);

    expect(fn () => (new LessonVideoService)->getVideoMetadata('missing-video'))
        ->toThrow(LessonVideoOperationException::class, 'Video tidak ditemukan.');
});
