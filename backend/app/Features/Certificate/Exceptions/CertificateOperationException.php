<?php

namespace App\Features\Certificate\Exceptions;

use RuntimeException;
use Throwable;

final class CertificateOperationException extends RuntimeException
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
