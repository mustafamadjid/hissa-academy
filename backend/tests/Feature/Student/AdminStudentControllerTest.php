<?php

use App\Features\Certificate\Models\Certificate;
use App\Features\Course\Models\Course;
use App\Features\Lesson\Models\Lesson;
use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use App\Features\UserProgress\Models\UserProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns paginated students with search filter sort and summary fields', function () {
    $this->actingAs(adminStudentAdminUser());

    User::factory()->create([
        'full_name' => 'Budi Student',
        'email' => 'budi.student@example.com',
        'role_id' => adminStudentRole('student')->id,
    ]);
    $student = User::factory()->create([
        'full_name' => 'Alya Student',
        'email' => 'alya.student@example.com',
        'role_id' => adminStudentRole('student')->id,
    ]);

    $response = $this->getJson('/api/v1/admin/students?search=alya&sort_by=full_name&sort_direction=asc&limit=5&page=1');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Daftar student berhasil diambil.')
        ->assertJsonPath('meta.current_page', 1)
        ->assertJsonPath('meta.per_page', 5)
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.id', $student->id)
        ->assertJsonPath('data.0.full_name', 'Alya Student')
        ->assertJsonPath('data.0.email', 'alya.student@example.com')
        ->assertJsonPath('data.0.role.name', 'student')
        ->assertJsonMissingPath('data.0.password')
        ->assertJsonMissingPath('data.0.remember_token');
});

it('returns validation errors for invalid student list query', function () {
    $this->actingAs(adminStudentAdminUser());

    $response = $this->getJson('/api/v1/admin/students?sort_by=password&sort_direction=sideways&limit=200&page=0');

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'sort_by',
            'sort_direction',
            'limit',
            'page',
        ]);
});

it('returns a student profile with learning activity summary', function () {
    $this->actingAs(adminStudentAdminUser());

    $student = User::factory()->create([
        'full_name' => 'Student HISSA',
        'email' => 'student.hissa@example.com',
        'role_id' => adminStudentRole('student')->id,
    ]);
    $course = Course::factory()->create();
    $lesson = Lesson::factory()->create(['course_id' => $course->id]);
    UserProgress::factory()->create([
        'user_id' => $student->id,
        'lesson_id' => $lesson->id,
        'status' => 'completed',
        'total_watched_seconds' => 420,
    ]);
    Certificate::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'issued',
    ]);

    $response = $this->getJson("/api/v1/admin/students/{$student->id}");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Detail student berhasil diambil.')
        ->assertJsonPath('data.id', $student->id)
        ->assertJsonPath('data.full_name', 'Student HISSA')
        ->assertJsonPath('data.email', 'student.hissa@example.com')
        ->assertJsonPath('data.learning_summary.total_progress_records', 1)
        ->assertJsonPath('data.learning_summary.completed_lessons', 1)
        ->assertJsonPath('data.learning_summary.in_progress_lessons', 0)
        ->assertJsonPath('data.learning_summary.total_watched_seconds', 420)
        ->assertJsonPath('data.learning_summary.total_certificates', 1)
        ->assertJsonMissingPath('data.password');
});

it('returns not found when a student does not exist', function () {
    $this->actingAs(adminStudentAdminUser());

    $response = $this->getJson('/api/v1/admin/students/00000000-0000-0000-0000-000000000000');

    $response->assertNotFound()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Student tidak ditemukan.');
});

it('returns student progress grouped by course and lesson', function () {
    $this->actingAs(adminStudentAdminUser());

    $student = User::factory()->create(['role_id' => adminStudentRole('student')->id]);
    $course = Course::factory()->create([
        'course_name' => 'Laravel Basics',
        'status' => 'active',
    ]);
    $completedLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'title' => 'Routing',
        'position' => 1,
    ]);
    $availableLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'title' => 'Controllers',
        'position' => 2,
    ]);
    UserProgress::factory()->create([
        'user_id' => $student->id,
        'lesson_id' => $completedLesson->id,
        'status' => 'completed',
        'last_position_seconds' => 300,
        'total_watched_seconds' => 600,
    ]);

    $response = $this->getJson("/api/v1/admin/students/{$student->id}/progress");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Progress student berhasil diambil.')
        ->assertJsonPath('data.0.course.id', $course->id)
        ->assertJsonPath('data.0.course.name', 'Laravel Basics')
        ->assertJsonPath('data.0.summary.total_lessons', 2)
        ->assertJsonPath('data.0.summary.completed_lessons', 1)
        ->assertJsonPath('data.0.summary.progress_percentage', 50)
        ->assertJsonPath('data.0.lessons.0.id', $completedLesson->id)
        ->assertJsonPath('data.0.lessons.0.progress.status', 'completed')
        ->assertJsonPath('data.0.lessons.0.progress.total_watched_seconds', 600)
        ->assertJsonPath('data.0.lessons.1.id', $availableLesson->id)
        ->assertJsonPath('data.0.lessons.1.progress.status', 'not_started');
});

it('returns student certificates with course data', function () {
    $this->actingAs(adminStudentAdminUser());

    $student = User::factory()->create(['role_id' => adminStudentRole('student')->id]);
    $course = Course::factory()->create(['course_name' => 'Laravel Basics']);
    $certificate = Certificate::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'certificate_number' => 'HISSA-2026-ABC12345',
        'status' => 'issued',
    ]);

    $response = $this->getJson("/api/v1/admin/students/{$student->id}/certificates?limit=5&page=1");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Daftar sertifikat student berhasil diambil.')
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.id', $certificate->id)
        ->assertJsonPath('data.0.certificate_number', 'HISSA-2026-ABC12345')
        ->assertJsonPath('data.0.status', 'issued')
        ->assertJsonPath('data.0.course.id', $course->id)
        ->assertJsonPath('data.0.course.name', 'Laravel Basics');
});

it('rejects admin student endpoints when the user is not an admin', function () {
    $this->actingAs(User::factory()->create([
        'role_id' => adminStudentRole('student')->id,
    ]));

    $response = $this->getJson('/api/v1/admin/students');

    $response->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Anda tidak memiliki akses.');
});

function adminStudentAdminUser(): User
{
    return User::factory()->create([
        'role_id' => adminStudentRole('admin')->id,
    ]);
}

function adminStudentRole(string $roleName): Role
{
    return Role::query()->firstOrCreate(['name' => $roleName]);
}
