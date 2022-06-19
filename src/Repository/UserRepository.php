<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;

class UserRepository extends AbstractRepository
{
    public function getUserByEmail(string $email): ?User
    {
        $user = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }

    public function getUserByEmailAndNotUuid(string $email, string $uuid): ?User
    {
        $user = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->andWhere('u.uuid != :uuid')
            ->setParameter('email', $email)
            ->setParameter('uuid', $uuid, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();

        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }

    protected function getEntityClass(): string
    {
        return User::class;
    }
}
