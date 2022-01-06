<?php

declare(strict_types=1);

namespace App\Service\Validator;

use App\DataTransferObject\UserDto;

interface UserValidatorInterface
{
    public function validateUser(UserDto $userDto): void;
}
