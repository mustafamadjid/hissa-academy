<?php

use App\Features\Course\Contracts\CourseRepositoryContract;
use App\Features\Course\DTOs\CourseListQueryData;
use App\Features\Course\Exceptions\CourseOperationException;
use App\Features\Course\Models\Course;
use App\Features\Course\Services\CourseService;
use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use App\GlobalExceptions\AuthorizationException;
use App\Helper\EnsureAdminForService;
use Illuminate\Pagination\LengthAwarePaginator;

it('uses the shared admin helper for authorization checks', function () {
    $service = new ReflectionClass(CourseService::class);
    $constructorParameterTypes = array_map(
        fn (ReflectionParameter $parameter): ?string => $parameter->getType()?->getName(),
        $service->getConstructor()->getParameters(),
    );

    expect($constructorParameterTypes)->toContain(EnsureAdminForService::class)
        ->and($service->hasMethod('ensureAdmin'))->toBeFalse();
});

it('returns paginated courses from the repository', function () {
    $query = new CourseListQueryData(search: 'laravel');
    $paginator = new LengthAwarePaginator([], 0, 10, 1, [
        'path' => '/api/v1/admin/courses',
    ]);
    $repository = Mockery::mock(CourseRepositoryContract::class);
    $repository->shouldReceive('all')
        ->once()
        ->with($query)
        ->andReturn($paginator);

    $service = makeCourseService($repository);

    expect($service->all($query))->toBe($paginator);
});

it('returns a course detail from the repository', function () {
    $course = new Course(['course_name' => 'Laravel Basics']);
    $repository = Mockery::mock(CourseRepositoryContract::class);
    $repository->shouldReceive('findById')
        ->once()
        ->with('course-uuid')
        ->andReturn($course);

    $service = makeCourseService($repository);

    expect($service->findById('course-uuid'))->toBe($course);
});

it('returns a course detail with lessons from the repository', function () {
    $course = new Course(['course_name' => 'Laravel Basics']);
    $repository = Mockery::mock(CourseRepositoryContract::class);
    $repository->shouldReceive('findWithLessonsById')
        ->once()
        ->with('course-uuid')
        ->andReturn($course);

    $service = makeCourseService($repository);

    expect($service->findWithLessonsById('course-uuid'))->toBe($course);
});

it('creates a course through the repository', function () {
    $payload = validServiceCoursePayload();
    $course = new Course($payload);
    $repository = Mockery::mock(CourseRepositoryContract::class);
    $repository->shouldReceive('create')
        ->once()
        ->with($payload)
        ->andReturn($course);

    $service = makeCourseService($repository);

    expect($service->create($payload, adminActor()))->toBe($course);
});

it('updates a course through the repository', function () {
    $payload = ['course_name' => 'Updated Course'];
    $course = new Course($payload);
    $repository = Mockery::mock(CourseRepositoryContract::class);
    $repository->shouldReceive('update')
        ->once()
        ->with('course-uuid', $payload)
        ->andReturn($course);

    $service = makeCourseService($repository);

    expect($service->update('course-uuid', $payload, adminActor()))->toBe($course);
});

it('deletes a course through the repository', function () {
    $repository = Mockery::mock(CourseRepositoryContract::class);
    $repository->shouldReceive('delete')
        ->once()
        ->with('course-uuid')
        ->andReturnTrue();

    $service = makeCourseService($repository);

    expect($service->delete('course-uuid', adminActor()))->toBeTrue();
});

it('rejects course creation when the actor is not an admin', function () {
    $repository = Mockery::mock(CourseRepositoryContract::class);
    $repository->shouldReceive('create')->never();

    $service = makeCourseService($repository);

    expect(fn () => $service->create(validServiceCoursePayload(), studentActor()))
        ->toThrow(AuthorizationException::class, 'Anda tidak memiliki akses.');
});

it('rejects course updates when the actor is missing', function () {
    $repository = Mockery::mock(CourseRepositoryContract::class);
    $repository->shouldReceive('update')->never();

    $service = makeCourseService($repository);

    expect(fn () => $service->update('course-uuid', ['status' => 'active'], null))
        ->toThrow(AuthorizationException::class, 'Anda tidak memiliki akses.');
});

it('rejects course deletion when the actor is not an admin', function () {
    $repository = Mockery::mock(CourseRepositoryContract::class);
    $repository->shouldReceive('delete')->never();

    $service = makeCourseService($repository);

    expect(fn () => $service->delete('course-uuid', studentActor()))
        ->toThrow(AuthorizationException::class, 'Anda tidak memiliki akses.');
});

it('wraps repository errors in a course operation exception', function () {
    $query = new CourseListQueryData;
    $repository = Mockery::mock(CourseRepositoryContract::class);
    $repository->shouldReceive('all')
        ->once()
        ->with($query)
        ->andThrow(new RuntimeException('database failed'));

    $service = makeCourseService($repository);

    expect(fn () => $service->all($query))
        ->toThrow(CourseOperationException::class, 'Gagal mengambil daftar course.');
});

function validServiceCoursePayload(array $overrides = []): array
{
    return array_merge([
        'course_name' => 'Laravel Basics',
        'description' => 'Introductory Laravel course',
        'minimum_score' => 70,
        'status' => 'draft',
    ], $overrides);
}

function makeCourseService(CourseRepositoryContract $repository): CourseService
{
    return new CourseService($repository, new EnsureAdminForService);
}

function adminActor(): User
{
    return actorWithRole('admin');
}

function studentActor(): User
{
    return actorWithRole('student');
}

function actorWithRole(string $roleName): User
{
    $user = new User;
    $user->setRelation('role', new Role(['name' => $roleName]));

    return $user;
}
