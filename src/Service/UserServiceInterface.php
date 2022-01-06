<?php

declare(strict_types=1);

namespace App\Service;

use App\DataTransferObject\UserDto;
use App\Entity\User;
use App\Repository\Result\PaginatedItemsResult;

interface UserServiceInterface
{
    public function getUserByUuid(string $uuid): User;

    public function getRecentUsersWithPagination(int $page, int $usersPerPage): PaginatedItemsResult;

    public function createUser(UserDto $userDto): User;

    public function updateUser(UserDto $userDto): User;
}
