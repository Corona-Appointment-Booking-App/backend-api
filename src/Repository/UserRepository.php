<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;

class UserRepository extends AbstractRepository
{
    public function getUserByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getUserByEmailAndNotUuid(string $email, string $uuid): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->andWhere('u.uuid != :uuid')
            ->setParameter('email', $email)
            ->setParameter('uuid', $uuid, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    protected function getEntityClass(): string
    {
        return User::class;
    }
}
