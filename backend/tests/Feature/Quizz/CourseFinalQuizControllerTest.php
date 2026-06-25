<?php

use App\Features\Course\Models\Course;
use App\Features\Quizz\Models\Answer;
use App\Features\Quizz\Models\Question;
use App\Features\Quizz\Models\Quizz;
use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns the final quiz configuration for a course', function () {
    $course = Course::factory()->create();
    $quiz = Quizz::factory()->create([
        'course_id' => $course->id,
        'quiz_name' => 'Final Quiz Laravel',
        'is_active' => true,
    ]);

    $response = $this->getJson("/api/v1/admin/courses/{$course->id}/quiz");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Konfigurasi quiz berhasil diambil.')
        ->assertJsonPath('data.id', $quiz->id)
        ->assertJsonPath('data.course_id', $course->id)
        ->assertJsonPath('data.quiz_name', 'Final Quiz Laravel')
        ->assertJsonPath('data.is_active', true)
        ->assertJsonMissingPath('data.created_at')
        ->assertJsonMissingPath('data.updated_at');
});

it('returns not found when retrieving a quiz for a missing course', function () {
    $response = $this->getJson('/api/v1/admin/courses/00000000-0000-0000-0000-000000000000/quiz');

    $response->assertNotFound()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Course tidak ditemukan.');
});

it('returns not found when the course has no final quiz', function () {
    $course = Course::factory()->create();

    $response = $this->getJson("/api/v1/admin/courses/{$course->id}/quiz");

    $response->assertNotFound()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Quiz tidak ditemukan.');
});

it('creates the final quiz for a course', function () {
    $this->actingAs(quizzAdminUser());
    $course = Course::factory()->create();

    $response = $this->postJson("/api/v1/admin/courses/{$course->id}/quiz", validFinalQuizPayload());

    $response->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Quiz berhasil dibuat.')
        ->assertJsonPath('data.course_id', $course->id)
        ->assertJsonPath('data.quiz_name', 'Final Quiz Laravel')
        ->assertJsonPath('data.is_active', true)
        ->assertJsonMissingPath('data.created_at');

    $this->assertDatabaseHas('quizzs', [
        'course_id' => $course->id,
        'quiz_name' => 'Final Quiz Laravel',
        'is_active' => true,
    ]);
});

it('returns validation errors when creating a final quiz with invalid payload', function () {
    $this->actingAs(quizzAdminUser());
    $course = Course::factory()->create();

    $response = $this->postJson("/api/v1/admin/courses/{$course->id}/quiz", [
        'quiz_name' => '',
        'is_active' => 'yes',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'quiz_name',
            'is_active',
        ]);
});

it('rejects final quiz creation when the user is not an admin', function () {
    $this->actingAs(quizzStudentUser());
    $course = Course::factory()->create();

    $response = $this->postJson("/api/v1/admin/courses/{$course->id}/quiz", validFinalQuizPayload());

    $response->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Anda tidak memiliki akses.');

    $this->assertDatabaseMissing('quizzs', [
        'course_id' => $course->id,
        'quiz_name' => 'Final Quiz Laravel',
    ]);
});

it('updates a quiz configuration', function () {
    $this->actingAs(quizzAdminUser());
    $quiz = Quizz::factory()->create([
        'quiz_name' => 'Old Final Quiz',
        'is_active' => false,
    ]);

    $response = $this->patchJson("/api/v1/admin/quizzes/{$quiz->id}", [
        'quiz_name' => 'Updated Final Quiz',
        'is_active' => true,
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Quiz berhasil diperbarui.')
        ->assertJsonPath('data.id', $quiz->id)
        ->assertJsonPath('data.quiz_name', 'Updated Final Quiz')
        ->assertJsonPath('data.is_active', true);

    $this->assertDatabaseHas('quizzs', [
        'id' => $quiz->id,
        'quiz_name' => 'Updated Final Quiz',
        'is_active' => true,
    ]);
});

it('soft deletes a quiz', function () {
    $this->actingAs(quizzAdminUser());
    $quiz = Quizz::factory()->create();

    $response = $this->deleteJson("/api/v1/admin/quizzes/{$quiz->id}");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Quiz berhasil dinonaktifkan.');

    $this->assertSoftDeleted('quizzs', [
        'id' => $quiz->id,
    ]);
});

it('returns questions and answer options for a quiz', function () {
    $this->actingAs(quizzAdminUser());
    $quiz = Quizz::factory()->create();
    $question = Question::factory()->create([
        'quizz_id' => $quiz->id,
        'question' => 'Apa itu Laravel?',
        'position' => 1,
        'image_url' => null,
    ]);
    Answer::factory()->create([
        'question_id' => $question->id,
        'answer' => 'Framework PHP',
        'is_correct' => true,
    ]);
    Answer::factory()->create([
        'question_id' => $question->id,
        'answer' => 'Database',
        'is_correct' => false,
    ]);

    $response = $this->getJson("/api/v1/admin/quizzes/{$quiz->id}/questions");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Daftar pertanyaan quiz berhasil diambil.')
        ->assertJsonPath('data.0.id', $question->id)
        ->assertJsonPath('data.0.question', 'Apa itu Laravel?')
        ->assertJsonPath('data.0.position', 1)
        ->assertJsonPath('data.0.answers.0.answer', 'Framework PHP')
        ->assertJsonPath('data.0.answers.0.is_correct', true)
        ->assertJsonPath('data.0.answers.1.answer', 'Database')
        ->assertJsonPath('data.0.answers.1.is_correct', false);
});

it('creates multiple questions with answers in one request', function () {
    $this->actingAs(quizzAdminUser());
    $quiz = Quizz::factory()->create();

    $response = $this->postJson(
        "/api/v1/admin/quizzes/{$quiz->id}/questions",
        validBatchQuestionsPayload()
    );

    $response->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Pertanyaan quiz berhasil ditambahkan.')
        ->assertJsonPath('data.0.quizz_id', $quiz->id)
        ->assertJsonPath('data.0.question', 'Apa itu Laravel?')
        ->assertJsonPath('data.0.answers.0.answer', 'Framework PHP')
        ->assertJsonPath('data.1.question', 'Apa fungsi migration?');

    $this->assertDatabaseHas('questions', [
        'quizz_id' => $quiz->id,
        'question' => 'Apa itu Laravel?',
        'position' => 1,
    ]);
    $this->assertDatabaseHas('answers_options', [
        'answer' => 'Framework PHP',
        'is_correct' => true,
    ]);
    $this->assertDatabaseHas('questions', [
        'quizz_id' => $quiz->id,
        'question' => 'Apa fungsi migration?',
        'position' => 2,
    ]);
});

it('does not persist partial questions when batch question validation fails', function () {
    $this->actingAs(quizzAdminUser());
    $quiz = Quizz::factory()->create();

    $response = $this->postJson("/api/v1/admin/quizzes/{$quiz->id}/questions", [
        'questions' => [
            [
                'question' => 'Apa itu Laravel?',
                'position' => 1,
                'answers' => [
                    ['answer' => 'Framework PHP', 'is_correct' => true],
                ],
            ],
            [
                'question' => 'Payload rusak',
                'position' => 2,
                'answers' => [],
            ],
        ],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'questions.1.answers',
        ]);

    $this->assertDatabaseMissing('questions', [
        'quizz_id' => $quiz->id,
        'question' => 'Apa itu Laravel?',
    ]);
});

it('rejects batch question creation when the user is not an admin', function () {
    $this->actingAs(quizzStudentUser());
    $quiz = Quizz::factory()->create();

    $response = $this->postJson(
        "/api/v1/admin/quizzes/{$quiz->id}/questions",
        validBatchQuestionsPayload()
    );

    $response->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Anda tidak memiliki akses.');

    $this->assertDatabaseMissing('questions', [
        'quizz_id' => $quiz->id,
        'question' => 'Apa itu Laravel?',
    ]);
});

function validFinalQuizPayload(array $overrides = []): array
{
    return array_merge([
        'quiz_name' => 'Final Quiz Laravel',
        'is_active' => true,
    ], $overrides);
}

function validBatchQuestionsPayload(array $overrides = []): array
{
    return array_merge([
        'questions' => [
            [
                'question' => 'Apa itu Laravel?',
                'position' => 1,
                'answers' => [
                    ['answer' => 'Framework PHP', 'is_correct' => true],
                    ['answer' => 'Database', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Apa fungsi migration?',
                'position' => 2,
                'answers' => [
                    ['answer' => 'Mengelola struktur database', 'is_correct' => true],
                    ['answer' => 'Mengirim email', 'is_correct' => false],
                ],
            ],
        ],
    ], $overrides);
}

function quizzAdminUser(): User
{
    return quizzUserWithRole('admin');
}

function quizzStudentUser(): User
{
    return quizzUserWithRole('student');
}

function quizzUserWithRole(string $roleName): User
{
    $role = Role::query()->firstOrCreate(['name' => $roleName]);

    return User::factory()->create([
        'role_id' => $role->id,
    ]);
}
