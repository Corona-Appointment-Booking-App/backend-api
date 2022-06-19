<?php

declare(strict_types=1);

namespace App\Exception;

use Doctrine\ORM\EntityNotFoundException;

class TestCenterNotFoundException extends EntityNotFoundException
{
    private const MESSAGE = 'test center with seo slug %s was not found.';

    public function __construct(string $seoSlug, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $seoSlug), $code, $previous);
    }
}
