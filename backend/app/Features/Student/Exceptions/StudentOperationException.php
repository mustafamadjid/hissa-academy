<?php

namespace App\Features\Student\Exceptions;

use RuntimeException;
use Throwable;

final class StudentOperationException extends RuntimeException
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
