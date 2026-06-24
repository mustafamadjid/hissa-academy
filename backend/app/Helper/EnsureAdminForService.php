<?php
namespace App\Helper;

use App\Features\User\Enums\UserRole;
use App\Features\User\Models\User;
use App\GlobalExceptions\AuthorizationException;

final class EnsureAdminForService
{
    public function ensureAdmin(?User $actor): void
    {
        if ($actor === null || $actor->role?->name !== UserRole::ADMIN->value) {
            throw new AuthorizationException();
        }
    }
}
?>
