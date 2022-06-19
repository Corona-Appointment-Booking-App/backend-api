<?php

declare(strict_types=1);

namespace App\Exception;

use Doctrine\ORM\EntityNotFoundException;

class BookingNotFoundException extends EntityNotFoundException
{
    private const MESSAGE = 'booking with uuid %s was not found.';

    public function __construct(string $uuid, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $uuid), $code, $previous);
    }
}
