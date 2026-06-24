<?php

namespace App\Features\LessonVideo\Exceptions;

use RuntimeException;
use Throwable;

final class LessonVideoOperationException extends RuntimeException
{
    public function __construct(string $message = 'Operasi lesson video gagal.', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
