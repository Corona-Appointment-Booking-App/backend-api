<?php

declare(strict_types=1);

namespace App\Service;

use App\DataTransferObject\UserDto;
use App\Entity\User;
use App\Exception\UserAlreadyExistsException;
use App\Exception\UserNotFoundException;
use App\Repository\Result\PaginatedItemsResult;
use App\Repository\UserRepository;
use App\Service\Util\SanitizerInterface;
use App\Service\Validator\UserValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService implements UserServiceInterface
{
    private UserRepository $userRepository;
    
    private EntityManagerInterface $entityManager;

    private UserValidatorInterface $userValidator;

    private UserPasswordHasherInterface $userPasswordHasher;

    private SanitizerInterface $htmlSanitizer;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserValidatorInterface $userValidator,
        UserPasswordHasherInterface $userPasswordHasher,
        SanitizerInterface $htmlSanitizer
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->userValidator = $userValidator;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->htmlSanitizer = $htmlSanitizer;
    }

    public function getUserByUuid(string $uuid): User
    {
        /** @var User $user */
        $user = $this->userRepository->getItemByUuid($uuid);

        if (!$user instanceof User) {
            throw new UserNotFoundException($uuid);
        }

        return $user;
    }

    public function getRecentUsersWithPagination(int $page, int $usersPerPage): PaginatedItemsResult
    {
        $query = $this->userRepository->getRecentItemsQuery();

        return $this->userRepository->getPaginatedItemsForQuery($query, $page, $usersPerPage);
    }

    public function createUser(UserDto $userDto): User
    {
        $this->userValidator->validateUser($userDto);

        $userEmail = $this->htmlSanitizer->sanitize($userDto->getEmail());

        $user = new User();
        $user->setEmail($userEmail);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $userDto->getPassword()));
        $user->setRoles([User::ROLE_ADMIN]);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(null);

        $this->validateIsEmailExisting($userEmail);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function updateUser(UserDto $userDto): User
    {
        $this->userValidator->validateUser($userDto);

        $userEmail = $this->htmlSanitizer->sanitize($userDto->getEmail());

        $user = $this->getUserByUuid($userDto->getId());
        $user->setEmail($userEmail);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $userDto->getPassword()));
        $user->setRoles([User::ROLE_ADMIN]);
        $user->setUpdatedAt(new \DateTimeImmutable());

        $this->validateIsEmailExistingNotForUuid($userEmail, $user->getUuid()->toRfc4122());

        $this->entityManager->flush();

        return $user;
    }

    private function validateIsEmailExisting(string $email): void
    {
        $isExisting = $this->userRepository->getUserByEmail($email);

        if ($isExisting) {
            throw new UserAlreadyExistsException($email);
        }
    }

    private function validateIsEmailExistingNotForUuid(string $email, string $uuid): void
    {
        $isExisting = $this->userRepository->getUserByEmailAndNotUuid($email, $uuid);

        if ($isExisting) {
            throw new UserAlreadyExistsException($email);
        }
    }
}
