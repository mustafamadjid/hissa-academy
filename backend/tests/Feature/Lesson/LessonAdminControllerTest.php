<?php

use App\Features\Course\Models\Course;
use App\Features\Lesson\Models\Lesson;
use App\Features\LessonVideo\Models\LessonVideo;
use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns lessons for a course ordered by position', function () {
    $this->actingAs(lessonAdminUser());

    $course = Course::factory()->create();
    Lesson::factory()->create([
        'course_id' => $course->id,
        'title' => 'Second Lesson',
        'position' => 2,
        'is_required' => false,
    ]);
    $firstLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'title' => 'First Lesson',
        'position' => 1,
        'is_required' => true,
    ]);

    $response = $this->getJson("/api/v1/admin/courses/{$course->id}/lessons");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Daftar lesson berhasil diambil.')
        ->assertJsonPath('data.0.id', $firstLesson->id)
        ->assertJsonPath('data.0.title', 'First Lesson')
        ->assertJsonPath('data.0.position', 1)
        ->assertJsonMissingPath('data.0.created_at');
});

it('creates a lesson with a youtube video url and shifts existing positions', function () {
    $this->actingAs(lessonAdminUser());
    $course = Course::factory()->create();
    Lesson::factory()->create([
        'course_id' => $course->id,
        'position' => 1,
    ]);

    $response = $this->postJson("/api/v1/admin/courses/{$course->id}/lessons", validLessonPayload([
        'position' => 1,
        'youtube_video_id' => 'dQw4w9WgXcQ',
    ]));

    $response->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Lesson berhasil dibuat.')
        ->assertJsonPath('data.title', 'Laravel Routing')
        ->assertJsonPath('data.position', 1)
        ->assertJsonPath('data.video.youtube_video_id', 'dQw4w9WgXcQ')
        ->assertJsonPath('data.video.video_url', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ');

    $this->assertDatabaseHas('lessons', [
        'title' => 'Laravel Routing',
        'course_id' => $course->id,
        'position' => 1,
        'is_required' => true,
    ]);
    $this->assertDatabaseHas('lessons', [
        'course_id' => $course->id,
        'position' => 2,
    ]);
    $this->assertDatabaseHas('lesson_videos', [
        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'duration_seconds' => 0,
    ]);
});

it('returns validation errors when creating a lesson with invalid payload', function () {
    $this->actingAs(lessonAdminUser());
    $course = Course::factory()->create();

    $response = $this->postJson("/api/v1/admin/courses/{$course->id}/lessons", [
        'title' => '',
        'youtube_video_id' => '',
        'position' => 0,
        'is_required' => 'yes',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'title',
            'youtube_video_id',
            'position',
            'is_required',
        ]);
});

it('returns lesson detail with video metadata', function () {
    $this->actingAs(lessonAdminUser());

    $lesson = Lesson::factory()->create([
        'title' => 'Laravel Middleware',
        'position' => 3,
        'is_required' => true,
    ]);
    LessonVideo::factory()->create([
        'lesson_id' => $lesson->id,
        'video_url' => 'https://www.youtube.com/watch?v=abc123XYZ09',
        'duration_seconds' => 420,
    ]);

    $response = $this->getJson("/api/v1/admin/lessons/{$lesson->id}");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Detail lesson berhasil diambil.')
        ->assertJsonPath('data.id', $lesson->id)
        ->assertJsonPath('data.title', 'Laravel Middleware')
        ->assertJsonPath('data.video.youtube_video_id', 'abc123XYZ09')
        ->assertJsonPath('data.video.duration_seconds', 420);
});

it('updates a lesson and reorders positions inside its course', function () {
    $this->actingAs(lessonAdminUser());
    $course = Course::factory()->create();
    $firstLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'position' => 1,
    ]);
    $secondLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'title' => 'Old Title',
        'position' => 2,
        'is_required' => true,
    ]);
    $thirdLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'position' => 3,
    ]);

    $response = $this->patchJson("/api/v1/admin/lessons/{$secondLesson->id}", [
        'title' => 'Updated Lesson',
        'position' => 1,
        'is_required' => false,
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Lesson berhasil diperbarui.')
        ->assertJsonPath('data.title', 'Updated Lesson')
        ->assertJsonPath('data.position', 1)
        ->assertJsonPath('data.is_required', false);

    $this->assertDatabaseHas('lessons', [
        'id' => $secondLesson->id,
        'title' => 'Updated Lesson',
        'position' => 1,
        'is_required' => false,
    ]);
    $this->assertDatabaseHas('lessons', [
        'id' => $firstLesson->id,
        'position' => 2,
    ]);
    $this->assertDatabaseHas('lessons', [
        'id' => $thirdLesson->id,
        'position' => 3,
    ]);
});

it('reorders lessons inside a course when requested by an admin', function () {
    $this->actingAs(lessonAdminUser());
    $course = Course::factory()->create();
    $firstLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'position' => 1,
    ]);
    $secondLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'position' => 2,
    ]);
    $thirdLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'position' => 3,
    ]);

    $response = $this->patchJson("/api/v1/admin/courses/{$course->id}/lessons/reorder", [
        'lessons' => [
            ['id' => $thirdLesson->id, 'position' => 1],
            ['id' => $firstLesson->id, 'position' => 2],
            ['id' => $secondLesson->id, 'position' => 3],
        ],
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Lesson berhasil diurutkan.');

    $this->assertDatabaseHas('lessons', [
        'id' => $thirdLesson->id,
        'course_id' => $course->id,
        'position' => 1,
    ]);
    $this->assertDatabaseHas('lessons', [
        'id' => $firstLesson->id,
        'course_id' => $course->id,
        'position' => 2,
    ]);
    $this->assertDatabaseHas('lessons', [
        'id' => $secondLesson->id,
        'course_id' => $course->id,
        'position' => 3,
    ]);
});

it('soft deletes a lesson when requested by an admin', function () {
    $this->actingAs(lessonAdminUser());
    $lesson = Lesson::factory()->create();

    $response = $this->deleteJson("/api/v1/admin/lessons/{$lesson->id}");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Lesson berhasil dihapus.');

    $this->assertSoftDeleted('lessons', [
        'id' => $lesson->id,
    ]);
});

it('rejects lesson creation when the user is not an admin', function () {
    $this->actingAs(lessonStudentUser());
    $course = Course::factory()->create();

    $response = $this->postJson("/api/v1/admin/courses/{$course->id}/lessons", validLessonPayload());

    $response->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Anda tidak memiliki akses.');

    $this->assertDatabaseMissing('lessons', [
        'title' => 'Laravel Routing',
    ]);
});

it('returns not found when the course does not exist', function () {
    $this->actingAs(lessonAdminUser());

    $response = $this->getJson('/api/v1/admin/courses/00000000-0000-0000-0000-000000000000/lessons');

    $response->assertNotFound()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Course tidak ditemukan.');
});

it('returns not found when the lesson does not exist', function () {
    $this->actingAs(lessonAdminUser());

    $response = $this->getJson('/api/v1/admin/lessons/00000000-0000-0000-0000-000000000000');

    $response->assertNotFound()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Lesson tidak ditemukan.');
});

function validLessonPayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'Laravel Routing',
        'youtube_video_id' => 'dQw4w9WgXcQ',
        'position' => 1,
        'is_required' => true,
    ], $overrides);
}

function lessonAdminUser(): User
{
    return lessonUserWithRole('admin');
}

function lessonStudentUser(): User
{
    return lessonUserWithRole('student');
}

function lessonUserWithRole(string $roleName): User
{
    $role = Role::query()->firstOrCreate(['name' => $roleName]);

    return User::factory()->create([
        'role_id' => $role->id,
    ]);
}
