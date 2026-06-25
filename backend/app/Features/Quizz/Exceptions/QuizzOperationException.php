<?php

namespace App\Features\Quizz\Exceptions;

use RuntimeException;
use Throwable;

final class QuizzOperationException extends RuntimeException
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
