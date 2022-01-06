<?php

declare(strict_types=1);

namespace App\Service\Util;

interface SanitizerInterface
{
    public function sanitize(string $content): string;
}
