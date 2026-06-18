<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'public_uuid' => (string) Str::uuid(),
            'role_id' => fn () => Role::query()->firstOrCreate(['name' => 'user'])->id,
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'full_name' => fake()->name(),
            'birth_date' => fake()->optional()->date(),
            'avatar_url' => fake()->optional()->imageUrl(),
            'password' => static::$password ??= Hash::make('password'),
        ];
    }
}
