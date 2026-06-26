<?php

use App\Features\Course\Models\Course;
use App\Features\Lesson\Models\Lesson;
use App\Features\Quizz\Models\Answer;
use App\Features\Quizz\Models\Question;
use App\Features\Quizz\Models\Quizz;
use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use App\Features\UserProgress\Models\UserProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns an unlocked course quiz after all required lessons are completed', function () {
    $student = studentQuizUser();
    $this->actingAs($student);

    $course = Course::factory()->create([
        'course_name' => 'Laravel Basics',
        'minimum_score' => 75,
        'status' => 'active',
    ]);
    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'is_required' => true,
    ]);
    UserProgress::factory()->create([
        'user_id' => $student->id,
        'lesson_id' => $lesson->id,
        'status' => 'completed',
        'completed_at' => now(),
    ]);
    $quiz = Quizz::factory()->create([
        'course_id' => $course->id,
        'quiz_name' => 'Final Quiz Laravel',
        'is_active' => true,
    ]);
    Question::factory()->count(2)->create(['quizz_id' => $quiz->id]);

    $response = $this->getJson("/api/v1/courses/{$course->id}/quiz");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Quiz berhasil diambil.')
        ->assertJsonPath('data.uuid', $quiz->id)
        ->assertJsonPath('data.name', 'Final Quiz Laravel')
        ->assertJsonPath('data.minimum_score', 75)
        ->assertJsonPath('data.total_questions', 2)
        ->assertJsonPath('data.attempt_policy.attempts_used', 0);
});

it('forbids course quiz access when a required lesson is not completed', function () {
    $this->actingAs(studentQuizUser());

    $course = Course::factory()->create(['status' => 'active']);
    Lesson::factory()->create([
        'course_id' => $course->id,
        'is_required' => true,
    ]);
    Quizz::factory()->create(['course_id' => $course->id, 'is_active' => true]);

    $response = $this->getJson("/api/v1/courses/{$course->id}/quiz");

    $response->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Quiz belum dapat diakses.')
        ->assertJsonPath('error.code', 'QUIZ_LOCKED')
        ->assertJsonPath('error.details.reason', 'REQUIRED_LESSONS_NOT_COMPLETED');
});

it('creates and returns a quiz attempt without exposing correct answers', function () {
    $student = studentQuizUser();
    $this->actingAs($student);

    [$quiz, $question, $correctAnswer] = studentQuizReadyQuiz($student);
    Answer::factory()->create([
        'question_id' => $question->id,
        'answer' => 'Wrong option',
        'is_correct' => false,
    ]);

    $createResponse = $this->postJson("/api/v1/quizzes/{$quiz->id}/attempts", []);

    $createResponse->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Quiz attempt berhasil dibuat.')
        ->assertJsonPath('data.quiz.uuid', $quiz->id)
        ->assertJsonPath('data.status', 'in_progress')
        ->assertJsonPath('data.questions.0.uuid', $question->id)
        ->assertJsonPath('data.questions.0.options.0.uuid', $correctAnswer->id)
        ->assertJsonMissingPath('data.questions.0.options.0.is_correct');

    $attemptUuid = $createResponse->json('data.uuid');

    $getResponse = $this->getJson("/api/v1/quiz-attempts/{$attemptUuid}");

    $getResponse->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Quiz attempt berhasil diambil.')
        ->assertJsonPath('data.uuid', $attemptUuid)
        ->assertJsonPath('data.score', null)
        ->assertJsonPath('data.questions.0.selected_option_uuid', null)
        ->assertJsonMissingPath('data.questions.0.options.0.is_correct');
});

it('submits a passing attempt and issues a certificate', function () {
    $student = studentQuizUser();
    $this->actingAs($student);
    $this->travelTo(now()->startOfSecond());

    [$quiz, $question, $correctAnswer] = studentQuizReadyQuiz($student, minimumScore: 75, points: 10);
    $attemptUuid = $this->postJson("/api/v1/quizzes/{$quiz->id}/attempts", [])->json('data.uuid');

    $response = $this->postJson("/api/v1/quiz-attempts/{$attemptUuid}/submit", [
        'answers' => [
            [
                'question_uuid' => $question->id,
                'selected_option_uuid' => $correctAnswer->id,
            ],
        ],
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Quiz berhasil disubmit dan user dinyatakan lulus.')
        ->assertJsonPath('data.attempt_uuid', $attemptUuid)
        ->assertJsonPath('data.score', 100)
        ->assertJsonPath('data.minimum_score', 75)
        ->assertJsonPath('data.status', 'passed')
        ->assertJsonPath('data.result.correct_answers', 1)
        ->assertJsonPath('data.result.incorrect_answers', 0)
        ->assertJsonPath('data.certificate.status', 'issued');

    $this->assertDatabaseHas('certificates', [
        'user_id' => $student->id,
        'course_id' => $quiz->course_id,
        'status' => 'issued',
    ]);
});

it('submits a failing attempt without issuing a certificate and rejects resubmission', function () {
    $student = studentQuizUser();
    $this->actingAs($student);

    [$quiz, $question] = studentQuizReadyQuiz($student, minimumScore: 75);
    $wrongAnswer = Answer::factory()->create([
        'question_id' => $question->id,
        'is_correct' => false,
    ]);
    $attemptUuid = $this->postJson("/api/v1/quizzes/{$quiz->id}/attempts", [])->json('data.uuid');

    $response = $this->postJson("/api/v1/quiz-attempts/{$attemptUuid}/submit", [
        'answers' => [
            [
                'question_uuid' => $question->id,
                'selected_option_uuid' => $wrongAnswer->id,
            ],
        ],
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Quiz berhasil disubmit, tetapi nilai belum memenuhi batas kelulusan.')
        ->assertJsonPath('data.score', 0)
        ->assertJsonPath('data.status', 'failed')
        ->assertJsonPath('data.certificate', null);

    $this->assertDatabaseMissing('certificates', [
        'user_id' => $student->id,
        'course_id' => $quiz->course_id,
    ]);

    $this->postJson("/api/v1/quiz-attempts/{$attemptUuid}/submit", [
        'answers' => [
            [
                'question_uuid' => $question->id,
                'selected_option_uuid' => $wrongAnswer->id,
            ],
        ],
    ])->assertStatus(409)
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'ATTEMPT_ALREADY_SUBMITTED');
});

function studentQuizUser(): User
{
    return User::factory()->create([
        'role_id' => studentQuizRole('student')->id,
    ]);
}

function studentQuizRole(string $roleName): Role
{
    return Role::query()->firstOrCreate(['name' => $roleName]);
}

/**
 * @return array{0: Quizz, 1: Question, 2: Answer}
 */
function studentQuizReadyQuiz(User $student, int $minimumScore = 75, int $points = 1): array
{
    $course = Course::factory()->create([
        'minimum_score' => $minimumScore,
        'status' => 'active',
    ]);
    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'is_required' => true,
    ]);
    UserProgress::factory()->create([
        'user_id' => $student->id,
        'lesson_id' => $lesson->id,
        'status' => 'completed',
        'completed_at' => now(),
    ]);
    $quiz = Quizz::factory()->create([
        'course_id' => $course->id,
        'is_active' => true,
    ]);
    $question = Question::factory()->create([
        'quizz_id' => $quiz->id,
        'points' => $points,
        'position' => 1,
    ]);
    $correctAnswer = Answer::factory()->create([
        'question_id' => $question->id,
        'answer' => 'Correct option',
        'is_correct' => true,
    ]);

    return [$quiz, $question, $correctAnswer];
}
