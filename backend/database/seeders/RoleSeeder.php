<?php

namespace Database\Seeders;

use App\Features\User\Enums\UserRole;
use App\Features\User\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (UserRole::cases() as $role) {
            $roleModel = Role::withTrashed()->firstOrCreate([
                'name' => $role->value,
            ]);

            if ($roleModel->trashed()) {
                $roleModel->restore();
            }
        }
    }
}
