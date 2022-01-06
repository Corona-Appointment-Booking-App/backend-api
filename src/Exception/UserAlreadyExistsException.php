<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class UserAlreadyExistsException extends \RuntimeException implements HttpExceptionInterface
{
    private const MESSAGE = 'user with email %s already exists.';

    public function __construct(
        string $email,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct(sprintf(static::MESSAGE, $email), $code, $previous);
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
