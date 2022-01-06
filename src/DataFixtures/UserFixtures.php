<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataTransferObject\UserDto;
use App\Service\UserServiceInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    private UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function load(ObjectManager $manager): void
    {
        $userDto = (new UserDto())->fromArray([
            'email' => 'admin@corona.test',
            'password' => '123456',
        ]);

        $this->userService->createUser($userDto);
    }
}
