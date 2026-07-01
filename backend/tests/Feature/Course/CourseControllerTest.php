<?php

use App\Features\Course\Models\Course;
use App\Features\Lesson\Models\Lesson;
use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns paginated courses with a manual json response format', function () {
    $this->actingAs(adminUser());

    Course::factory()->create([
        'course_name' => 'PHP Fundamentals',
        'description' => 'Programming basics',
        'minimum_score' => 60,
        'status' => 'active',
    ]);
    $course = Course::factory()->create([
        'course_name' => 'Laravel Basics',
        'description' => 'Framework introduction',
        'minimum_score' => 70,
        'status' => 'active',
    ]);

    $response = $this->getJson('/api/v1/admin/courses?search=Laravel&limit=5&page=1');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Daftar course berhasil diambil.')
        ->assertJsonPath('meta.current_page', 1)
        ->assertJsonPath('meta.per_page', 5)
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.id', $course->id)
        ->assertJsonPath('data.0.name', 'Laravel Basics')
        ->assertJsonMissingPath('data.0.created_at')
        ->assertJsonMissingPath('data.0.updated_at');
});

it('creates a course', function () {
    $this->actingAs(adminUser());

    $response = $this->postJson('/api/v1/admin/courses', validApiCoursePayload());

    $response->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Course berhasil dibuat.')
        ->assertJsonPath('data.name', 'Laravel Basics')
        ->assertJsonMissingPath('data.created_at');

    $this->assertDatabaseHas('courses', [
        'course_name' => 'Laravel Basics',
        'status' => 'draft',
    ]);
});

it('returns validation errors when creating a course with invalid payload', function () {
    $this->actingAs(adminUser());

    $response = $this->postJson('/api/v1/admin/courses', [
        'course_name' => '',
        'description' => '',
        'minimum_score' => 101,
        'status' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'course_name',
            'description',
            'minimum_score',
            'status',
        ]);
});

it('returns a course detail', function () {
    $this->actingAs(adminUser());

    $course = Course::factory()->create([
        'course_name' => 'Laravel Basics',
    ]);
    $secondLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'title' => 'Routing',
        'position' => 2,
    ]);
    $firstLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'title' => 'Introduction',
        'position' => 1,
    ]);

    $response = $this->getJson("/api/v1/admin/courses/{$course->id}");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Detail course berhasil diambil.')
        ->assertJsonPath('data.id', $course->id)
        ->assertJsonPath('data.name', 'Laravel Basics')
        ->assertJsonPath('data.total_lessons', 2)
        ->assertJsonPath('data.lessons.0.id', $firstLesson->id)
        ->assertJsonPath('data.lessons.0.title', 'Introduction')
        ->assertJsonPath('data.lessons.1.id', $secondLesson->id)
        ->assertJsonPath('data.lessons.1.title', 'Routing');
});

it('returns not found when a course does not exist', function () {
    $this->actingAs(adminUser());

    $response = $this->getJson('/api/v1/admin/courses/00000000-0000-0000-0000-000000000000');

    $response->assertNotFound()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Course tidak ditemukan.');
});

it('updates a course', function () {
    $this->actingAs(adminUser());

    $course = Course::factory()->create([
        'course_name' => 'Old Course',
        'status' => 'draft',
    ]);

    $response = $this->patchJson("/api/v1/admin/courses/{$course->id}", [
        'course_name' => 'Updated Course',
        'status' => 'active',
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Course berhasil diperbarui.')
        ->assertJsonPath('data.name', 'Updated Course')
        ->assertJsonPath('data.status', 'active');

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'course_name' => 'Updated Course',
        'status' => 'active',
    ]);
});

it('soft deletes a course', function () {
    $this->actingAs(adminUser());

    $course = Course::factory()->create();

    $response = $this->deleteJson("/api/v1/admin/courses/{$course->id}");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Course berhasil dihapus.');

    $this->assertSoftDeleted('courses', [
        'id' => $course->id,
    ]);
});

it('rejects course creation when the user is not an admin', function () {
    $this->actingAs(studentUser());

    $response = $this->postJson('/api/v1/admin/courses', validApiCoursePayload());

    $response->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Anda tidak memiliki akses.');

    $this->assertDatabaseMissing('courses', [
        'course_name' => 'Laravel Basics',
    ]);
});

it('rejects course updates when the user is not authenticated', function () {
    $course = Course::factory()->create([
        'course_name' => 'Old Course',
    ]);

    $response = $this->patchJson("/api/v1/admin/courses/{$course->id}", [
        'course_name' => 'Updated Course',
    ]);

    $response->assertUnauthorized();

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'course_name' => 'Old Course',
    ]);
});

it('rejects course deletion when the user is not an admin', function () {
    $this->actingAs(studentUser());
    $course = Course::factory()->create();

    $response = $this->deleteJson("/api/v1/admin/courses/{$course->id}");

    $response->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Anda tidak memiliki akses.');

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'deleted_at' => null,
    ]);
});

function validApiCoursePayload(array $overrides = []): array
{
    return array_merge([
        'course_name' => 'Laravel Basics',
        'description' => 'Introductory Laravel course',
        'minimum_score' => 70,
        'status' => 'draft',
    ], $overrides);
}

function adminUser(): User
{
    return userWithRole('admin');
}

function studentUser(): User
{
    return userWithRole('student');
}

function userWithRole(string $roleName): User
{
    $role = Role::query()->firstOrCreate(['name' => $roleName]);

    return User::factory()->create([
        'role_id' => $role->id,
    ]);
}
