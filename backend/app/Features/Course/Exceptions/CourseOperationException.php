<?php

namespace App\Features\Course\Exceptions;

use RuntimeException;
use Throwable;

final class CourseOperationException extends RuntimeException
{
    public function __construct(string $message = 'Operasi course gagal.', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
