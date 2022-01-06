<?php

declare(strict_types=1);

namespace App\Exception;

use Doctrine\ORM\EntityNotFoundException;

class UserNotFoundException extends EntityNotFoundException
{
    private const MESSAGE = 'user with id %s was not found.';

    public function __construct(string $uuid, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf(static::MESSAGE, $uuid), $code, $previous);
    }
}
