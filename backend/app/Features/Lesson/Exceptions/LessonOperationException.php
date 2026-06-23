<?php

namespace App\Features\Lesson\Exceptions;

use RuntimeException;
use Throwable;

final class LessonOperationException extends RuntimeException
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, previous: $previous);
    }
}
