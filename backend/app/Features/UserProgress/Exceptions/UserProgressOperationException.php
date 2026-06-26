<?php

namespace App\Features\UserProgress\Exceptions;

use RuntimeException;
use Throwable;

final class UserProgressOperationException extends RuntimeException
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, previous: $previous);
    }
}
