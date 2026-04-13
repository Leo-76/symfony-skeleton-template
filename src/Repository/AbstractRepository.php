<?php

declare(strict_types=1);

namespace App\Repository;

use App\Pagination\DoctrinePaginator;
use App\Pagination\PaginatedResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Base repository with shared pagination and search helpers.
 *
 * @template T of object
 * @extends  ServiceEntityRepository<T>
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * Paginate a QueryBuilder result.
     *
     * @return PaginatedResult<T>
     */
    protected function paginate(QueryBuilder $qb, int $page, int $limit = DoctrinePaginator::DEFAULT_LIMIT): PaginatedResult
    {
        return (new DoctrinePaginator())->paginate($qb, $page, $limit);
    }

    /**
     * Save an entity (persist + flush).
     *
     * @param T $entity
     */
    public function save(object $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove an entity (remove + flush).
     *
     * @param T $entity
     */
    public function remove(object $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
