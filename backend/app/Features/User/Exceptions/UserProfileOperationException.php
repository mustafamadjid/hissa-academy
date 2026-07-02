<?php

namespace App\Features\User\Exceptions;

use RuntimeException;
use Throwable;

final class UserProfileOperationException extends RuntimeException
{
    public function __construct(
        string $message = 'Gagal mengambil profil user.',
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
