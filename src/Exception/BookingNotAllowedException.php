<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class BookingNotAllowedException extends \RuntimeException implements HttpExceptionInterface
{
    private const MESSAGE = 'booking for test center %s and time %s is not possible.';

    public function __construct(
        string $testCenterId,
        \DateTimeImmutable $time,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $message = sprintf(self::MESSAGE, $testCenterId, $time->format(\DateTimeImmutable::ATOM));

        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getHeaders(): array
    {
        return [];
    }
}
