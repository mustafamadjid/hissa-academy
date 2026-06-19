<?php

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
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
