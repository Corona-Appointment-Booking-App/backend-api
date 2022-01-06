<?php

declare(strict_types=1);

namespace App\Exception;

class BookingFinalizeTokenGeneratorException extends \RuntimeException
{
    private const MESSAGE = 'unable to generate token: %s.';

    public function __construct(string $errorMessage, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf(static::MESSAGE, $errorMessage), $code, $previous);
    }
}
