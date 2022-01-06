<?php

declare(strict_types=1);

namespace App\Service\Util;

class HtmlSanitizer implements SanitizerInterface
{
    public function sanitize(string $content): string
    {
        return trim(strip_tags($content));
    }
}
