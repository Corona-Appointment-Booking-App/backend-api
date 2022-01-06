<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EntityInterface;
use App\Repository\Result\PaginatedItemsResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EntityInterface|null find($id, $lockMode = null, $lockVersion = null)
 * @method EntityInterface|null findOneBy(array $criteria, array $orderBy = null)
 * @method EntityInterface[]    findAll()
 * @method EntityInterface[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, $this->getEntityClass());
    }

    public function getItemBySeoSlug(string $slug): ?EntityInterface
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.seoSlug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getItemByUuid(string $uuid): ?EntityInterface
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.uuid = :uuid')
            ->setParameter('uuid', $uuid, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getRecentItemsQuery(): Query
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.id', 'DESC')
            ->getQuery();
    }

    public function getTotalItemsCount(bool $onlyFromToday = false): int
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->select('count(i.id)');

        if ($onlyFromToday) {
            $queryBuilder->where('i.createdAt > CURRENT_DATE()');
        }

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function getPaginatedItemsForQuery(
        Query $query,
        int $page,
        int $itemsPerPage
    ): PaginatedItemsResult {
        $paginator = new Paginator($query);

        $totalItems = $paginator->count();
        $totalPages = (int) ceil($totalItems / $itemsPerPage);

        $paginator->getQuery()
            ->setFirstResult(($page * $itemsPerPage) - $itemsPerPage)
            ->setMaxResults($itemsPerPage);

        return (new PaginatedItemsResult())
               ->setCurrentPage($page)
               ->setItemsPerPage($itemsPerPage)
               ->setTotalItems($totalItems)
               ->setTotalPages($totalPages)
               ->setPaginator($paginator);
    }

    abstract protected function getEntityClass(): string;
}
