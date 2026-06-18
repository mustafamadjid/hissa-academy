<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $role = Role::query()->firstOrCreate(['name' => 'user']);

        User::factory()->create([
            'role_id' => $role->id,
            'username' => 'testuser',
            'full_name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
