<?php

use App\Features\Course\Models\Course;
use App\Features\Lesson\Models\Lesson;
use App\Features\LessonVideo\Models\LessonVideo;
use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('creates youtube metadata for a lesson', function () {
    $this->actingAs(lessonVideoAdminUser());
    $lesson = Lesson::factory()->create([
        'course_id' => Course::factory()->create(['status' => 'draft'])->id,
    ]);
    Http::fake([
        'https://www.googleapis.com/youtube/v3/videos*' => youtubeMetadataResponse('video-123'),
    ]);

    $response = $this->putJson("/api/v1/admin/lessons/{$lesson->id}/video", [
        'youtube_video_id' => 'video-123',
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Metadata video lesson berhasil disimpan.')
        ->assertJsonPath('data.lesson_id', $lesson->id)
        ->assertJsonPath('data.youtube_video_id', 'video-123')
        ->assertJsonPath('data.video_url', 'https://www.youtube.com/watch?v=video-123')
        ->assertJsonPath('data.title', 'Laravel Lesson');

    $this->assertDatabaseHas('lesson_videos', [
        'lesson_id' => $lesson->id,
        'video_id' => 'video-123',
        'title' => 'Laravel Lesson',
        'duration_seconds' => 3723,
        'privacy_status' => 'public',
    ]);
});

it('updates existing youtube metadata for a lesson', function () {
    $this->actingAs(lessonVideoAdminUser());
    $lesson = Lesson::factory()->create([
        'course_id' => Course::factory()->create(['status' => 'draft'])->id,
    ]);
    $video = LessonVideo::factory()->create([
        'lesson_id' => $lesson->id,
        'video_url' => 'https://www.youtube.com/watch?v=old-video',
        'video_id' => 'old-video',
        'title' => 'Old Title',
    ]);
    Http::fake([
        'https://www.googleapis.com/youtube/v3/videos*' => youtubeMetadataResponse('new-video'),
    ]);

    $response = $this->putJson("/api/v1/admin/lessons/{$lesson->id}/video", [
        'youtube_video_id' => 'new-video',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.id', $video->id)
        ->assertJsonPath('data.youtube_video_id', 'new-video')
        ->assertJsonPath('data.title', 'Laravel Lesson');

    $this->assertDatabaseHas('lesson_videos', [
        'id' => $video->id,
        'lesson_id' => $lesson->id,
        'video_id' => 'new-video',
        'video_url' => 'https://www.youtube.com/watch?v=new-video',
    ]);
});

it('deletes youtube metadata from a draft lesson', function () {
    $this->actingAs(lessonVideoAdminUser());
    $lesson = Lesson::factory()->create([
        'course_id' => Course::factory()->create(['status' => 'draft'])->id,
    ]);
    $video = LessonVideo::factory()->create([
        'lesson_id' => $lesson->id,
        'video_id' => 'video-123',
    ]);

    $response = $this->deleteJson("/api/v1/admin/lessons/{$lesson->id}/video");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Metadata video lesson berhasil dihapus.');

    $this->assertSoftDeleted('lesson_videos', [
        'id' => $video->id,
    ]);
});

it('rejects video metadata deletion when the lesson course is not draft', function () {
    $this->actingAs(lessonVideoAdminUser());
    $lesson = Lesson::factory()->create([
        'course_id' => Course::factory()->create(['status' => 'active'])->id,
    ]);
    LessonVideo::factory()->create([
        'lesson_id' => $lesson->id,
        'video_id' => 'video-123',
    ]);

    $response = $this->deleteJson("/api/v1/admin/lessons/{$lesson->id}/video");

    $response->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Metadata video hanya dapat dihapus dari lesson draft.');

    $this->assertDatabaseHas('lesson_videos', [
        'lesson_id' => $lesson->id,
        'video_id' => 'video-123',
        'deleted_at' => null,
    ]);
});

it('returns validation errors when youtube video id is invalid', function () {
    $this->actingAs(lessonVideoAdminUser());
    $lesson = Lesson::factory()->create();

    $response = $this->putJson("/api/v1/admin/lessons/{$lesson->id}/video", [
        'youtube_video_id' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['youtube_video_id']);
});

it('rejects video metadata changes when the user is not an admin', function () {
    $this->actingAs(lessonVideoStudentUser());
    $lesson = Lesson::factory()->create();

    $response = $this->putJson("/api/v1/admin/lessons/{$lesson->id}/video", [
        'youtube_video_id' => 'video-123',
    ]);

    $response->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Anda tidak memiliki akses.');

    $this->assertDatabaseMissing('lesson_videos', [
        'lesson_id' => $lesson->id,
    ]);
});

it('returns not found when the lesson does not exist', function () {
    $this->actingAs(lessonVideoAdminUser());

    $response = $this->putJson('/api/v1/admin/lessons/00000000-0000-0000-0000-000000000000/video', [
        'youtube_video_id' => 'video-123',
    ]);

    $response->assertNotFound()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Lesson tidak ditemukan.');
});

function youtubeMetadataResponse(string $videoId): mixed
{
    return Http::response([
        'items' => [[
            'id' => $videoId,
            'snippet' => [
                'title' => 'Laravel Lesson',
                'description' => 'Learn Laravel basics.',
                'channelTitle' => 'HISSA',
                'thumbnails' => [
                    'medium' => ['url' => 'https://img.youtube.com/medium.jpg'],
                ],
            ],
            'contentDetails' => [
                'duration' => 'PT1H2M3S',
            ],
            'status' => [
                'privacyStatus' => 'public',
            ],
        ]],
    ]);
}

function lessonVideoAdminUser(): User
{
    return lessonVideoUserWithRole('admin');
}

function lessonVideoStudentUser(): User
{
    return lessonVideoUserWithRole('student');
}

function lessonVideoUserWithRole(string $roleName): User
{
    $role = Role::query()->firstOrCreate(['name' => $roleName]);

    return User::factory()->create([
        'role_id' => $role->id,
    ]);
}
