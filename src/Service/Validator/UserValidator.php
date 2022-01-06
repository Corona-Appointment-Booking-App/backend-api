<?php

declare(strict_types=1);

namespace App\Service\Validator;

use App\DataTransferObject\UserDto;
use App\Exception\UserValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserValidator implements UserValidatorInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateUser(UserDto $userDto): void
    {
        $violations = $this->validator->validate($userDto);

        if ($violations->count()) {
            throw new UserValidationException($violations);
        }
    }
}
