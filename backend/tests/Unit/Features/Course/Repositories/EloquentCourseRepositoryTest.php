<?php

use App\Features\Course\DTOs\CourseListQueryData;
use App\Features\Course\Models\Course;
use App\Features\Course\Repositories\EloquentCourseRepository;
use App\Features\Lesson\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('returns paginated courses filtered by search and sorted by the requested column', function () {
    Course::factory()->create([
        'course_name' => 'PHP Fundamentals',
        'description' => 'Programming basics',
        'minimum_score' => 60,
        'status' => 'active',
    ]);
    $second = Course::factory()->create([
        'course_name' => 'Advanced Laravel',
        'description' => 'Framework patterns',
        'minimum_score' => 80,
        'status' => 'active',
    ]);
    $first = Course::factory()->create([
        'course_name' => 'Laravel Basics',
        'description' => 'Framework introduction',
        'minimum_score' => 70,
        'status' => 'active',
    ]);
    Course::factory()->create([
        'course_name' => 'Laravel Deleted',
        'description' => 'Soft deleted course',
        'minimum_score' => 75,
        'status' => 'inactive',
    ])->delete();

    $repository = new EloquentCourseRepository;

    $courses = $repository->all(new CourseListQueryData(
        search: 'Laravel',
        sortBy: 'minimum_score',
        sortDirection: 'asc',
        limit: 10,
        page: 1,
    ));

    expect($courses->total())->toBe(2)
        ->and($courses->pluck('id')->all())->toBe([
            $first->id,
            $second->id,
        ]);
});

it('finds a course by id and returns null when it is missing', function () {
    $course = Course::factory()->create();
    $repository = new EloquentCourseRepository;

    expect($repository->findById($course->id)?->is($course))->toBeTrue()
        ->and($repository->findById('00000000-0000-0000-0000-000000000000'))->toBeNull();
});

it('finds a course with its lessons ordered by position and their total', function () {
    $course = Course::factory()->create();
    $secondLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'position' => 2,
    ]);
    $firstLesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'position' => 1,
    ]);
    $repository = new EloquentCourseRepository;

    $result = $repository->findWithLessonsById($course->id);

    expect($result)->not->toBeNull()
        ->and($result->relationLoaded('lessons'))->toBeTrue()
        ->and($result->lessons_count)->toBe(2)
        ->and($result->lessons->pluck('id')->all())->toBe([
            $firstLesson->id,
            $secondLesson->id,
        ]);
});

it('creates a course', function () {
    $repository = new EloquentCourseRepository;

    $course = $repository->create(validCoursePayload());

    expect($course->exists)->toBeTrue()
        ->and($course->course_name)->toBe('Laravel Basics');

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'course_name' => 'Laravel Basics',
    ]);
});

it('updates a course and returns null when it is missing', function () {
    $course = Course::factory()->create([
        'course_name' => 'Old Course',
    ]);
    $repository = new EloquentCourseRepository;

    $updated = $repository->update($course->id, [
        'course_name' => 'Updated Course',
        'status' => 'active',
    ]);

    expect($updated?->course_name)->toBe('Updated Course')
        ->and($repository->update('00000000-0000-0000-0000-000000000000', [
            'course_name' => 'Missing Course',
        ]))->toBeNull();

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'course_name' => 'Updated Course',
    ]);
});

it('soft deletes a course and returns false when it is missing', function () {
    $course = Course::factory()->create();
    $repository = new EloquentCourseRepository;

    expect($repository->delete($course->id))->toBeTrue()
        ->and($repository->delete('00000000-0000-0000-0000-000000000000'))->toBeFalse();

    $this->assertSoftDeleted('courses', [
        'id' => $course->id,
    ]);
});

function validCoursePayload(array $overrides = []): array
{
    return array_merge([
        'course_name' => 'Laravel Basics',
        'description' => 'Introductory Laravel course',
        'minimum_score' => 70,
        'status' => 'draft',
    ], $overrides);
}
