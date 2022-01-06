<?php

declare(strict_types=1);

namespace App\Exception;

use Doctrine\ORM\EntityNotFoundException;

class OpeningDayNotFoundException extends EntityNotFoundException
{
    private const MESSAGE = 'opening day with day or uuid %s was not found.';

    public function __construct(string $dayOrUuid, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf(static::MESSAGE, $dayOrUuid), $code, $previous);
    }
}
