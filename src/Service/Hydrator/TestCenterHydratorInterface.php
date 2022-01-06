<?php

declare(strict_types=1);

namespace App\Service\Hydrator;

use App\DataTransferObject\TestCenterDto;

interface TestCenterHydratorInterface
{
    public function hydrateFromArray(array $payload): TestCenterDto;
}
