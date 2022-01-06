<?php

declare(strict_types=1);

namespace App\Service\Hydrator;

use App\DataTransferObject\UserDto;

interface UserHydratorInterface
{
    public function hydrateFromArray(array $payload): UserDto;
}
