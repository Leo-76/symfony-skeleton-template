<?php

declare(strict_types=1);

namespace App\Pagination;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Doctrine-backed paginator service.
 */
final class DoctrinePaginator
{
    public const DEFAULT_LIMIT = 20;

    /**
     * @template T of object
     *
     * @param QueryBuilder $qb
     * @param int          $page        1-based page number
     * @param int          $limit       items per page
     * @param bool         $fetchJoin   set to false when no joins are involved
     *
     * @return PaginatedResult<T>
     */
    public function paginate(
        QueryBuilder $qb,
        int          $page        = 1,
        int          $limit       = self::DEFAULT_LIMIT,
        bool         $fetchJoin   = true,
    ): PaginatedResult {
        $page  = max(1, $page);
        $limit = max(1, $limit);

        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        $paginator = new Paginator($qb->getQuery(), $fetchJoin);

        return new PaginatedResult(
            items:        iterator_to_array($paginator->getIterator()),
            totalItems:   $paginator->count(),
            currentPage:  $page,
            itemsPerPage: $limit,
        );
    }
}
