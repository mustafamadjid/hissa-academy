<?php

use App\Features\Course\DTOs\CourseListQueryData;

it('maps list query parameters to array', function () {
    $query = new CourseListQueryData(
        search: 'laravel',
        sortBy: 'course_name',
        sortDirection: 'asc',
        limit: 15,
        page: 2,
    );

    expect($query->toArray())->toBe([
        'search' => 'laravel',
        'sort_by' => 'course_name',
        'sort_direction' => 'asc',
        'limit' => 15,
        'page' => 2,
    ]);
});

it('keeps default pagination and sorting values', function () {
    $query = new CourseListQueryData;

    expect($query->toArray())->toBe([
        'search' => '',
        'sort_by' => 'created_at',
        'sort_direction' => 'desc',
        'limit' => 10,
        'page' => 1,
    ]);
});
