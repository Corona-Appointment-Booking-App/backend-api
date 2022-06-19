<?php

declare(strict_types=1);

namespace App\Exception;

class BookingFinalizeTokenValidationException extends \RuntimeException
{
    private const MESSAGE = 'the provided token %s is not valid expected %s.';

    public function __construct(
        string $providedToken,
        string $expectedToken,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $message = sprintf(self::MESSAGE, $providedToken, $expectedToken);

        parent::__construct($message, $code, $previous);
    }
}
