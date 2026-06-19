<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\LessonVideo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<LessonVideo>
 */
class LessonVideoFactory extends Factory
{
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
