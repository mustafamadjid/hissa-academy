<?php

namespace Database\Factories\Features\LessonVideo\Models;

use App\Features\Lesson\Models\Lesson;
use App\Features\LessonVideo\Models\LessonVideo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<LessonVideo>
 */
class LessonVideoFactory extends Factory
{
    protected $model = LessonVideo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'public_uuid' => (string) Str::uuid(),
            'lesson_id' => Lesson::factory(),
            'video_url' => fake()->url(),
            'duration_seconds' => fake()->numberBetween(60, 3600),
        ];
    }
}
