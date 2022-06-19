<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BookingAlreadyCancelledException extends BadRequestHttpException
{
    private const MESSAGE = 'booking with id %s is already cancelled.';

    public function __construct(
        string $bookingId,
        \Throwable $previous = null,
        int $code = 0,
        array $headers = []
    ) {
        $message = sprintf(self::MESSAGE, $bookingId);

        parent::__construct($message, $previous, $code, $headers);
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
