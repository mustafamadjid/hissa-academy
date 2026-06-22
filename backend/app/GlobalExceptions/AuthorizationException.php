<?php

namespace App\GlobalExceptions;

use RuntimeException;

final class AuthorizationException extends RuntimeException
{
    public function __construct(string $message = 'Anda tidak memiliki akses.')
    {
        parent::__construct($message);
    }
}
