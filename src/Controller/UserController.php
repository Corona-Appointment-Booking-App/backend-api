<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataTransferObject\UserDto;
use App\Entity\User;
use App\Service\Hydrator\UserHydratorInterface;
use App\Service\UserServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractApiController
{
    private UserServiceInterface $userService;

    private UserHydratorInterface $userHydrator;

    public function __construct(
        UserServiceInterface $userService,
        UserHydratorInterface $userHydrator
    ) {
        $this->userService = $userService;
        $this->userHydrator = $userHydrator;
    }

    #[Route('/api/user/id/{id}', name: 'api.user.get.id', methods: ['GET'])]
    public function getUserById(string $id): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $user = $this->userService->getUserByUuid($id);

        return $this->buildJsonSuccessResponse($user);
    }

    #[Route('/api/user/list/paginated/{page}/{perPage}', name: 'api.user.get.all.paginated', methods: ['GET'])]
    public function getAllUsersWithPagination(int $page = self::DEFAULT_PAGE, int $perPage = self::DEFAULT_LIST_LIMIT): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $result = $this->userService->getRecentUsersWithPagination($page, $perPage);

        return $this->buildJsonPaginationResponse($result, [User::GROUP_READ]);
    }

    #[Route('/api/user/create', name: 'api.user.create', methods: ['POST'])]
    public function createUser(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $userDto = $this->createUserDto($request);
        $createdUser = $this->userService->createUser($userDto);

        return $this->buildJsonSuccessResponse($createdUser);
    }

    #[Route('/api/user/update', name: 'api.user.update', methods: ['POST'])]
    public function updateUser(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $userDto = $this->createUserDto($request);
        $updatedUser = $this->userService->updateUser($userDto);

        return $this->buildJsonSuccessResponse($updatedUser);
    }

    private function createUserDto(Request $request): UserDto
    {
        $payload = $this->getPayloadFromRequest($request);

        return $this->userHydrator->hydrateFromArray($payload);
    }
}
