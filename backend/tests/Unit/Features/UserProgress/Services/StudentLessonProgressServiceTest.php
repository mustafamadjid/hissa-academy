<?php

use App\Features\Course\Models\Course;
use App\Features\Lesson\Contracts\LessonRepositoryContract;
use App\Features\Lesson\Models\Lesson;
use App\Features\LessonVideo\Models\LessonVideo;
use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use App\Features\UserProgress\Contracts\UserProgressRepositoryContract;
use App\Features\UserProgress\DTOs\LessonProgressHeartbeatData;
use App\Features\UserProgress\Models\UserProgress;
use App\Features\UserProgress\Services\StudentLessonProgressService;
use App\Helper\EnsureStudentForService;

it('uses the shared student helper for authorization checks', function () {
    $service = new ReflectionClass(StudentLessonProgressService::class);
    $constructorParameterTypes = array_map(
        fn (ReflectionParameter $parameter): ?string => $parameter->getType()?->getName(),
        $service->getConstructor()->getParameters(),
    );

    expect($constructorParameterTypes)->toContain(EnsureStudentForService::class);
});

it('stores completed progress when watched time reaches the completion threshold', function () {
    $student = progressStudentActor();
    $lesson = progressLessonWithCourse();

    $lessonRepository = Mockery::mock(LessonRepositoryContract::class);
    $lessonRepository->shouldReceive('findActiveCourseLessonById')
        ->once()
        ->with('lesson-uuid')
        ->andReturn($lesson);

    $progressRepository = Mockery::mock(UserProgressRepositoryContract::class);
    $progressRepository->shouldReceive('forUserLessons')
        ->once()
        ->with('student-uuid', ['lesson-uuid'])
        ->andReturn(collect());
    $progressRepository->shouldReceive('findForUserLesson')
        ->once()
        ->with('student-uuid', 'lesson-uuid')
        ->andReturnNull();
    $progressRepository->shouldReceive('saveForUserLesson')
        ->once()
        ->withArgs(function (string $userId, string $lessonId, array $attributes): bool {
            expect($userId)->toBe('student-uuid')
                ->and($lessonId)->toBe('lesson-uuid')
                ->and($attributes['last_position_seconds'])->toBe(95)
                ->and($attributes['total_watched_seconds'])->toBe(95)
                ->and($attributes['status'])->toBe('completed')
                ->and($attributes['completed_at'])->not->toBeNull();

            return true;
        })
        ->andReturn(new UserProgress([
            'lesson_id' => 'lesson-uuid',
            'last_position_seconds' => 95,
            'total_watched_seconds' => 95,
            'status' => 'completed',
        ]));

    $service = new StudentLessonProgressService($lessonRepository, $progressRepository, new EnsureStudentForService);

    $progress = $service->heartbeat(
        'lesson-uuid',
        new LessonProgressHeartbeatData(lastPositionSeconds: 95, watchedSeconds: 95),
        $student,
    );

    expect($progress?->status)->toBe('completed');
});

function progressStudentActor(): User
{
    $user = new User;
    $user->setAttribute('id', 'student-uuid');
    $user->setRelation('role', new Role(['name' => 'student']));

    return $user;
}

function progressLessonWithCourse(): Lesson
{
    $lesson = new Lesson([
        'course_id' => 'course-uuid',
        'title' => 'Introduction',
        'position' => 1,
        'is_required' => true,
    ]);
    $lesson->setAttribute('id', 'lesson-uuid');

    $course = new Course([
        'course_name' => 'Laravel Basics',
        'status' => 'active',
    ]);
    $course->setAttribute('id', 'course-uuid');
    $course->setRelation('lessons', collect([$lesson]));

    $video = new LessonVideo([
        'duration_seconds' => 100,
    ]);

    $lesson->setRelation('course', $course);
    $lesson->setRelation('video', $video);

    return $lesson;
}
