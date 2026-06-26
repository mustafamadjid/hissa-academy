<?php

use App\Features\Course\Contracts\CourseRepositoryContract;
use App\Features\Course\Models\Course;
use App\Features\Course\Services\StudentCourseService;
use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use App\Features\UserProgress\Contracts\UserProgressRepositoryContract;
use App\GlobalExceptions\AuthorizationException;
use App\Helper\EnsureStudentForService;
use Illuminate\Support\Collection;

it('uses the shared student helper for authorization checks', function () {
    $service = new ReflectionClass(StudentCourseService::class);
    $constructorParameterTypes = array_map(
        fn (ReflectionParameter $parameter): ?string => $parameter->getType()?->getName(),
        $service->getConstructor()->getParameters(),
    );

    expect($constructorParameterTypes)->toContain(EnsureStudentForService::class);
});

it('returns active course summaries with student progress', function () {
    $course = new Course([
        'course_name' => 'Laravel Basics',
        'description' => 'Introductory Laravel course',
        'minimum_score' => 70,
        'status' => 'active',
    ]);
    $course->setAttribute('id', 'course-uuid');
    $course->setRelation('lessons', collect([
        lessonModelForStudentCourseService('lesson-1'),
        lessonModelForStudentCourseService('lesson-2'),
    ]));

    $courseRepository = Mockery::mock(CourseRepositoryContract::class);
    $courseRepository->shouldReceive('activeCoursesWithLessons')
        ->once()
        ->andReturn(collect([$course]));

    $progressRepository = Mockery::mock(UserProgressRepositoryContract::class);
    $progressRepository->shouldReceive('forUserLessons')
        ->once()
        ->with('student-uuid', ['lesson-1', 'lesson-2'])
        ->andReturn(new Collection([
            'lesson-1' => progressModelForStudentCourseService('completed'),
        ]));

    $service = new StudentCourseService($courseRepository, $progressRepository, new EnsureStudentForService);

    expect($service->listAvailable(studentActorForStudentCourseService()))->toBe([
        [
            'id' => 'course-uuid',
            'name' => 'Laravel Basics',
            'description' => 'Introductory Laravel course',
            'minimum_score' => 70,
            'status' => 'active',
            'course_id' => 'course-uuid',
            'total_lessons' => 2,
            'completed_lessons' => 1,
            'progress_percentage' => 50,
        ],
    ]);
});

it('rejects course access when actor is not a student', function () {
    $courseRepository = Mockery::mock(CourseRepositoryContract::class);
    $courseRepository->shouldReceive('activeCoursesWithLessons')->never();
    $progressRepository = Mockery::mock(UserProgressRepositoryContract::class);
    $progressRepository->shouldReceive('forUserLessons')->never();

    $service = new StudentCourseService($courseRepository, $progressRepository, new EnsureStudentForService);

    expect(fn () => $service->listAvailable(adminActorForStudentCourseService()))
        ->toThrow(AuthorizationException::class, 'Anda tidak memiliki akses.');
});

function lessonModelForStudentCourseService(string $id)
{
    $lesson = new App\Features\Lesson\Models\Lesson([
        'title' => 'Lesson',
        'position' => 1,
        'is_required' => true,
    ]);
    $lesson->setAttribute('id', $id);

    return $lesson;
}

function progressModelForStudentCourseService(string $status)
{
    return new App\Features\UserProgress\Models\UserProgress([
        'status' => $status,
    ]);
}

function studentActorForStudentCourseService(): User
{
    return actorForStudentCourseService('student', 'student-uuid');
}

function adminActorForStudentCourseService(): User
{
    return actorForStudentCourseService('admin', 'admin-uuid');
}

function actorForStudentCourseService(string $roleName, string $id): User
{
    $user = new User;
    $user->setAttribute('id', $id);
    $user->setRelation('role', new Role(['name' => $roleName]));

    return $user;
}
