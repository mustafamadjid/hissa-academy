<?php

namespace Database\Factories\Features\User\Models;

use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

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
            'role_id' => fn () => Role::query()->firstOrCreate(['name' => 'user'])->id,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'full_name' => fake()->name(),
            'birth_date' => fake()->optional()->date(),
            'avatar_url' => fake()->optional()->imageUrl(),
            'password' => static::$password ??= Hash::make('password'),
        ];
    }
}
