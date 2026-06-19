<?php

namespace Database\Factories\Features\Certificate\Models;

use App\Features\Certificate\Models\Certificate;
use App\Features\Course\Models\Course;
use App\Features\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'issued_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'status' => fake()->randomElement(['issued', 'revoked']),
            'pdf_path' => 'certificates/' . fake()->uuid() . '.pdf',
        ];
    }
}
