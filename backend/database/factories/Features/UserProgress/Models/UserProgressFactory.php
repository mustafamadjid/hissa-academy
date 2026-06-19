<?php

namespace Database\Factories\Features\UserProgress\Models;

use App\Features\Lesson\Models\Lesson;
use App\Features\User\Models\User;
use App\Features\UserProgress\Models\UserProgress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserProgress>
 */
class UserProgressFactory extends Factory
{
    protected $model = UserProgress::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $lastPositionSeconds = fake()->numberBetween(0, 3600);

        return [
            'user_id' => User::factory(),
            'lesson_id' => Lesson::factory(),
            'last_position_seconds' => $lastPositionSeconds,
            'total_watched_seconds' => fake()->numberBetween($lastPositionSeconds, 7200),
            'status' => fake()->randomElement(['not_started', 'in_progress', 'completed']),
            'completed_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
