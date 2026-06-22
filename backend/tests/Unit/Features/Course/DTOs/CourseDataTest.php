<?php

use App\Features\Course\DTOs\CourseData;

it('maps course data to persistence attributes', function () {
    $data = new CourseData(
        courseName: 'Laravel Basics',
        description: 'Introductory Laravel course',
        minimumScore: 75.5,
        status: 'draft',
    );

    expect($data->toArray())->toBe([
        'course_name' => 'Laravel Basics',
        'description' => 'Introductory Laravel course',
        'minimum_score' => 75.5,
        'status' => 'draft',
    ]);
});
