<?php

namespace Database\Seeders;

use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $name = env('DEFAULT_ADMIN_NAME', 'Administrator');
        $email = env('DEFAULT_ADMIN_EMAIL');
        $password = env('DEFAULT_ADMIN_PASSWORD');

        if (! $email || ! $password) {
            throw new RuntimeException(
                'Default admin credentials are not configured.'
            );
        }

        $adminRole = Role::query()->firstOrCreate([
            'name' => 'admin',
        ]);

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'full_name' => $name,
                'role_id' => $adminRole->id,
                'password' => Hash::make($password),
            ],
        );
    }
}
