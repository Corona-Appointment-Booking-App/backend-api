<?php

declare(strict_types=1);

namespace App\Service\Hydrator;

use App\DataTransferObject\UserDto;

class UserHydrator implements UserHydratorInterface
{
    public function hydrateFromArray(array $payload): UserDto
    {
        return (new UserDto())->fromArray([
            'id' => (string) $payload['id'] ??= '',
            'email' => (string) $payload['email'] ??= '',
            'password' => (string) $payload['password'] ??= '',
        ]);
    }
}
