<?php

declare(strict_types=1);

namespace App\Exception;

use Doctrine\ORM\EntityNotFoundException;

class CityLocationNotFoundException extends EntityNotFoundException
{
    private const MESSAGE = 'city location with seo slug or uuid %s was not found.';

    public function __construct(string $seoSlugOrUuid, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $seoSlugOrUuid), $code, $previous);
    }
}
