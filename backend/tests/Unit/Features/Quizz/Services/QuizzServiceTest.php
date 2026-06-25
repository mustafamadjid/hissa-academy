<?php

use App\Features\Course\Models\Course;
use App\Features\Quizz\Contracts\QuizzRepositoryContract;
use App\Features\Quizz\DTOs\QuestionCreateData;
use App\Features\Quizz\DTOs\QuestionOptionCreateData;
use App\Features\Quizz\DTOs\QuizzCreateData;
use App\Features\Quizz\Exceptions\QuizzOperationException;
use App\Features\Quizz\Models\Question;
use App\Features\Quizz\Models\Quizz;
use App\Features\Quizz\Services\QuizzService;
use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use App\GlobalExceptions\AuthorizationException;
use App\Helper\EnsureAdminForService;

it('returns the final quiz for an existing course', function () {
    $course = new Course(['course_name' => 'Laravel Basics']);
    $course->id = 'course-uuid';
    $quiz = new Quizz(['quiz_name' => 'Final Quiz Laravel']);

    $repository = Mockery::mock(QuizzRepositoryContract::class);
    $repository->shouldReceive('findCourseById')
        ->once()
        ->with('course-uuid')
        ->andReturn($course);
    $repository->shouldReceive('findFinalQuizByCourse')
        ->once()
        ->with($course)
        ->andReturn($quiz);

    $service = makeQuizzService($repository);

    expect($service->findFinalQuizByCourse('course-uuid'))->toBe($quiz);
});

it('returns null when finding a final quiz for a missing course', function () {
    $repository = Mockery::mock(QuizzRepositoryContract::class);
    $repository->shouldReceive('findCourseById')
        ->once()
        ->with('missing-course')
        ->andReturnNull();
    $repository->shouldReceive('findFinalQuizByCourse')->never();

    $service = makeQuizzService($repository);

    expect($service->findFinalQuizByCourse('missing-course'))->toBeNull();
});

it('creates a final quiz through the repository', function () {
    $course = new Course(['course_name' => 'Laravel Basics']);
    $course->id = 'course-uuid';
    $data = new QuizzCreateData(
        quizName: 'Final Quiz Laravel',
        isActive: true,
    );
    $quiz = new Quizz([
        'course_id' => 'course-uuid',
        'quiz_name' => 'Final Quiz Laravel',
        'is_active' => true,
    ]);

    $repository = Mockery::mock(QuizzRepositoryContract::class);
    $repository->shouldReceive('findCourseById')
        ->once()
        ->with('course-uuid')
        ->andReturn($course);
    $repository->shouldReceive('createFinalQuiz')
        ->once()
        ->with($course, $data)
        ->andReturn($quiz);

    $service = makeQuizzService($repository);

    expect($service->createFinalQuiz('course-uuid', $data, quizzAdminActor()))->toBe($quiz);
});

it('returns null when creating a final quiz for a missing course', function () {
    $data = new QuizzCreateData(
        quizName: 'Final Quiz Laravel',
        isActive: true,
    );
    $repository = Mockery::mock(QuizzRepositoryContract::class);
    $repository->shouldReceive('findCourseById')
        ->once()
        ->with('missing-course')
        ->andReturnNull();
    $repository->shouldReceive('createFinalQuiz')->never();

    $service = makeQuizzService($repository);

    expect($service->createFinalQuiz('missing-course', $data, quizzAdminActor()))->toBeNull();
});

it('rejects final quiz creation when the actor is not an admin', function () {
    $repository = Mockery::mock(QuizzRepositoryContract::class);
    $repository->shouldReceive('findCourseById')->never();
    $repository->shouldReceive('createFinalQuiz')->never();
    $service = makeQuizzService($repository);

    expect(fn () => $service->createFinalQuiz(
        'course-uuid',
        new QuizzCreateData(quizName: 'Final Quiz Laravel', isActive: true),
        quizzStudentActor(),
    ))->toThrow(AuthorizationException::class, 'Anda tidak memiliki akses.');
});

it('wraps repository errors in a quizz operation exception', function () {
    $repository = Mockery::mock(QuizzRepositoryContract::class);
    $repository->shouldReceive('findCourseById')
        ->once()
        ->with('course-uuid')
        ->andThrow(new RuntimeException('database failed'));

    $service = makeQuizzService($repository);

    expect(fn () => $service->findFinalQuizByCourse('course-uuid'))
        ->toThrow(QuizzOperationException::class, 'Gagal mengambil konfigurasi quiz.');
});

it('updates a quiz through the repository', function () {
    $data = new QuizzCreateData(
        quizName: 'Updated Final Quiz',
        isActive: true,
    );
    $quiz = new Quizz([
        'quiz_name' => 'Updated Final Quiz',
        'is_active' => true,
    ]);
    $quiz->id = 'quiz-uuid';

    $repository = Mockery::mock(QuizzRepositoryContract::class);
    $repository->shouldReceive('findQuizById')
        ->once()
        ->with('quiz-uuid')
        ->andReturn($quiz);
    $repository->shouldReceive('updateQuiz')
        ->once()
        ->with($quiz, $data)
        ->andReturn($quiz);

    $service = makeQuizzService($repository);

    expect($service->updateQuiz('quiz-uuid', $data, quizzAdminActor()))->toBe($quiz);
});

it('soft deletes a quiz through the repository', function () {
    $quiz = new Quizz(['quiz_name' => 'Final Quiz']);
    $quiz->id = 'quiz-uuid';

    $repository = Mockery::mock(QuizzRepositoryContract::class);
    $repository->shouldReceive('findQuizById')
        ->once()
        ->with('quiz-uuid')
        ->andReturn($quiz);
    $repository->shouldReceive('deleteQuiz')
        ->once()
        ->with($quiz)
        ->andReturnTrue();

    $service = makeQuizzService($repository);

    expect($service->deleteQuiz('quiz-uuid', quizzAdminActor()))->toBeTrue();
});

it('returns questions for a quiz through the repository', function () {
    $quiz = new Quizz(['quiz_name' => 'Final Quiz']);
    $quiz->id = 'quiz-uuid';
    $question = new Question(['question' => 'Apa itu Laravel?']);

    $repository = Mockery::mock(QuizzRepositoryContract::class);
    $repository->shouldReceive('findQuizById')
        ->once()
        ->with('quiz-uuid')
        ->andReturn($quiz);
    $repository->shouldReceive('listQuestionsWithAnswers')
        ->once()
        ->with($quiz)
        ->andReturn(collect([$question]));

    $service = makeQuizzService($repository);

    expect($service->listQuestions('quiz-uuid', quizzAdminActor())->first())->toBe($question);
});

it('creates batch questions through the repository', function () {
    $quiz = new Quizz(['quiz_name' => 'Final Quiz']);
    $quiz->id = 'quiz-uuid';
    $data = [
        new QuestionCreateData(
            question: 'Apa itu Laravel?',
            position: 1,
            imageUrl: null,
            answers: [
                new QuestionOptionCreateData(answer: 'Framework PHP', isCorrect: true),
            ],
        ),
    ];
    $question = new Question(['question' => 'Apa itu Laravel?']);

    $repository = Mockery::mock(QuizzRepositoryContract::class);
    $repository->shouldReceive('findQuizById')
        ->once()
        ->with('quiz-uuid')
        ->andReturn($quiz);
    $repository->shouldReceive('createQuestionsWithAnswers')
        ->once()
        ->with($quiz, $data)
        ->andReturn(collect([$question]));

    $service = makeQuizzService($repository);

    expect($service->createQuestions('quiz-uuid', $data, quizzAdminActor())->first())->toBe($question);
});

it('rejects batch question creation when the actor is not an admin', function () {
    $repository = Mockery::mock(QuizzRepositoryContract::class);
    $repository->shouldReceive('findQuizById')->never();
    $repository->shouldReceive('createQuestionsWithAnswers')->never();
    $service = makeQuizzService($repository);

    expect(fn () => $service->createQuestions(
        'quiz-uuid',
        [
            new QuestionCreateData(
                question: 'Apa itu Laravel?',
                position: 1,
                imageUrl: null,
                answers: [
                    new QuestionOptionCreateData(answer: 'Framework PHP', isCorrect: true),
                ],
            ),
        ],
        quizzStudentActor(),
    ))->toThrow(AuthorizationException::class, 'Anda tidak memiliki akses.');
});

function makeQuizzService(QuizzRepositoryContract $repository): QuizzService
{
    return new QuizzService($repository, new EnsureAdminForService);
}

function quizzAdminActor(): User
{
    return quizzActorWithRole('admin');
}

function quizzStudentActor(): User
{
    return quizzActorWithRole('student');
}

function quizzActorWithRole(string $roleName): User
{
    $user = new User;
    $user->setRelation('role', new Role(['name' => $roleName]));

    return $user;
}
