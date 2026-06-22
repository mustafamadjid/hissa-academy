<?php

namespace Database\Factories\Features\Lesson\Models;

use App\Features\Course\Models\Course;
use App\Features\Lesson\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => fake()->sentence(3),
            'position' => fake()->numberBetween(1, 20),
            'is_required' => fake()->boolean(),
        ];
    }
}
