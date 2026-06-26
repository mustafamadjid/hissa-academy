<?php
namespace App\Helper;

use App\Features\User\Enums\UserRole;
use App\Features\User\Models\User;
use App\GlobalExceptions\AuthorizationException;

final class EnsureStudentForService
{
    public function ensureStudent(?User $actor): void
    {
        if ($actor === null || $actor->role?->name !== UserRole::STUDENT->value) {
            throw new AuthorizationException();
        }
    }
}
?>
