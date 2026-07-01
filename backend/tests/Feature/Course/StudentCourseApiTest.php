<?php

use App\Features\Course\Models\Course;
use App\Features\Lesson\Models\Lesson;
use App\Features\LessonVideo\Models\LessonVideo;
use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use App\Features\UserProgress\Models\UserProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns active courses available for a student', function () {
    $student = studentApiUser();
    $this->actingAs($student);

    $activeCourse = Course::factory()->create([
        'course_name' => 'Laravel Basics',
        'status' => 'active',
    ]);
    Course::factory()->create([
        'course_name' => 'Draft Course',
        'status' => 'draft',
    ]);
    $lesson = Lesson::factory()->create([
        'course_id' => $activeCourse->id,
        'position' => 1,
        'is_required' => true,
    ]);
    UserProgress::factory()->create([
        'user_id' => $student->id,
        'lesson_id' => $lesson->id,
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    $response = $this->getJson('/api/v1/courses');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Daftar course berhasil diambil.')
        ->assertJsonPath('data.0.id', $activeCourse->id)
        ->assertJsonPath('data.0.name', 'Laravel Basics')
        ->assertJsonPath('data.0.total_lessons', 1)
        ->assertJsonPath('data.0.completed_lessons', 1)
        ->assertJsonPath('data.0.progress_percentage', 100)
        ->assertJsonMissingPath('data.1');
});

it('returns course detail with ordered lesson lock status and progress', function () {
    $student = studentApiUser();
    $this->actingAs($student);

    $course = Course::factory()->create([
        'course_name' => 'Laravel Basics',
        'status' => 'active',
    ]);
    $firstLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'title' => 'Introduction',
        'position' => 1,
        'is_required' => true,
    ]);
    $secondLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'title' => 'Routing',
        'position' => 2,
        'is_required' => true,
    ]);
    UserProgress::factory()->create([
        'user_id' => $student->id,
        'lesson_id' => $firstLesson->id,
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    $response = $this->getJson("/api/v1/courses/{$course->id}");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Detail course berhasil diambil.')
        ->assertJsonPath('data.id', $course->id)
        ->assertJsonPath('data.lessons.0.id', $firstLesson->id)
        ->assertJsonPath('data.lessons.0.is_locked', false)
        ->assertJsonPath('data.lessons.0.progress.status', 'completed')
        ->assertJsonPath('data.lessons.1.id', $secondLesson->id)
        ->assertJsonPath('data.lessons.1.is_locked', false)
        ->assertJsonPath('data.progress_percentage', 50);
});

it('returns course progress summary for the active student', function () {
    $student = studentApiUser();
    $this->actingAs($student);

    $course = Course::factory()->create(['status' => 'active']);
    $firstLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'position' => 1,
    ]);
    Lesson::factory()->create([
        'course_id' => $course->id,
        'position' => 2,
    ]);
    UserProgress::factory()->create([
        'user_id' => $student->id,
        'lesson_id' => $firstLesson->id,
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    $response = $this->getJson("/api/v1/courses/{$course->id}/progress");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Progress course berhasil diambil.')
        ->assertJsonPath('data.course_id', $course->id)
        ->assertJsonPath('data.total_lessons', 2)
        ->assertJsonPath('data.completed_lessons', 1)
        ->assertJsonPath('data.progress_percentage', 50);
});

it('rejects course student endpoints for non students', function () {
    $this->actingAs(userWithApiRole('admin'));

    $response = $this->getJson('/api/v1/courses');

    $response->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Anda tidak memiliki akses.');
});

it('returns unlocked lesson detail for a student', function () {
    $student = studentApiUser();
    $this->actingAs($student);

    $course = Course::factory()->create(['status' => 'active']);
    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'title' => 'Introduction',
        'position' => 1,
    ]);
    LessonVideo::factory()->create([
        'lesson_id' => $lesson->id,
        'video_url' => 'https://www.youtube.com/watch?v=abc123XYZ09',
        'duration_seconds' => 300,
    ]);

    $response = $this->getJson("/api/v1/lessons/{$lesson->id}");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Detail lesson berhasil diambil.')
        ->assertJsonPath('data.id', $lesson->id)
        ->assertJsonPath('data.video.duration_seconds', 300)
        ->assertJsonPath('data.is_locked', false);
});

it('forbids loading a locked lesson', function () {
    $student = studentApiUser();
    $this->actingAs($student);

    $course = Course::factory()->create(['status' => 'active']);
    Lesson::factory()->create([
        'course_id' => $course->id,
        'position' => 1,
        'is_required' => true,
    ]);
    $lockedLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'position' => 2,
        'is_required' => true,
    ]);

    $response = $this->getJson("/api/v1/lessons/{$lockedLesson->id}");

    $response->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Lesson masih terkunci.');
});

it('returns and stores lesson progress heartbeat for a student', function () {
    $student = studentApiUser();
    $this->actingAs($student);

    $course = Course::factory()->create(['status' => 'active']);
    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'position' => 1,
    ]);
    LessonVideo::factory()->create([
        'lesson_id' => $lesson->id,
        'duration_seconds' => 100,
    ]);

    $postResponse = $this->postJson("/api/v1/lessons/{$lesson->id}/progress", [
        'last_position_seconds' => 95,
        'watched_seconds' => 95,
    ]);

    $postResponse->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Progress lesson berhasil disimpan.')
        ->assertJsonPath('data.last_position_seconds', 95)
        ->assertJsonPath('data.total_watched_seconds', 95)
        ->assertJsonPath('data.status', 'completed');

    $this->assertDatabaseHas('user_progress', [
        'user_id' => $student->id,
        'lesson_id' => $lesson->id,
        'last_position_seconds' => 95,
        'total_watched_seconds' => 95,
        'status' => 'completed',
    ]);

    $getResponse = $this->getJson("/api/v1/lessons/{$lesson->id}/progress");

    $getResponse->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Progress lesson berhasil diambil.')
        ->assertJsonPath('data.status', 'completed');
});

it('validates lesson progress heartbeat payload', function () {
    $this->actingAs(studentApiUser());
    $lesson = Lesson::factory()->create();

    $response = $this->postJson("/api/v1/lessons/{$lesson->id}/progress", [
        'last_position_seconds' => -1,
        'watched_seconds' => -5,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['last_position_seconds', 'watched_seconds']);
});

it('filters courses by search keyword', function () {
    $student = studentApiUser();
    $this->actingAs($student);

    $course1 = Course::factory()->create([
        'course_name' => 'Laravel Basics',
        'status' => 'active',
    ]);
    $course2 = Course::factory()->create([
        'course_name' => 'Advanced PHP',
        'status' => 'active',
    ]);
    Course::factory()->create([
        'course_name' => 'React Fundamentals',
        'status' => 'active',
    ]);

    $response = $this->getJson('/api/v1/courses?search=Laravel');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.0.name', 'Laravel Basics')
        ->assertJsonPath('meta.total', 1)
        ->assertJsonMissingPath('data.1');
});

it('returns paginated courses with default per_page', function () {
    $student = studentApiUser();
    $this->actingAs($student);

    Course::factory()->count(20)->create(['status' => 'active']);

    $response = $this->getJson('/api/v1/courses');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('meta.per_page', 15)
        ->assertJsonPath('meta.total', 20)
        ->assertJsonCount(15, 'data');
});

it('respects custom per_page parameter', function () {
    $student = studentApiUser();
    $this->actingAs($student);

    Course::factory()->count(20)->create(['status' => 'active']);

    $response = $this->getJson('/api/v1/courses?per_page=5');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('meta.per_page', 5)
        ->assertJsonCount(5, 'data');
});

it('returns pagination links in response', function () {
    $student = studentApiUser();
    $this->actingAs($student);

    Course::factory()->count(20)->create(['status' => 'active']);

    $response = $this->getJson('/api/v1/courses?page=1&per_page=10');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('meta.current_page', 1)
        ->assertJsonPath('meta.last_page', 2)
        ->assertJsonPath('links.first', fn ($url) => str_contains($url, 'page=1'))
        ->assertJsonPath('links.next', fn ($url) => str_contains($url, 'page=2'));
});

it('reads search from query parameter not json body', function () {
    $student = studentApiUser();
    $this->actingAs($student);

    Course::factory()->create(['course_name' => 'Laravel Basics', 'status' => 'active']);
    Course::factory()->create(['course_name' => 'PHP Advanced', 'status' => 'active']);

    $response = $this->getJson('/api/v1/courses?search=Laravel');

    $response->assertOk()
        ->assertJsonPath('data.0.name', 'Laravel Basics')
        ->assertJsonPath('meta.total', 1);
});

function studentApiUser(): User
{
    return userWithApiRole('student');
}

function userWithApiRole(string $roleName): User
{
    $role = Role::query()->firstOrCreate(['name' => $roleName]);

    return User::factory()->create([
        'role_id' => $role->id,
    ]);
}
