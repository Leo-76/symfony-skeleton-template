<?php

declare(strict_types=1);

namespace App\Pagination;

/**
 * Immutable pagination result object.
 *
 * @template T
 */
final class PaginatedResult
{
    /**
     * @param T[]  $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int   $totalItems,
        public readonly int   $currentPage,
        public readonly int   $itemsPerPage,
    ) {
    }

    public function getTotalPages(): int
    {
        if ($this->itemsPerPage <= 0) {
            return 1;
        }

        return (int) ceil($this->totalItems / $this->itemsPerPage);
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getTotalPages();
    }

    public function getPreviousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    public function getNextPage(): int
    {
        return min($this->getTotalPages(), $this->currentPage + 1);
    }

    public function isEmpty(): bool
    {
        return [] === $this->items;
    }

    public function getOffset(): int
    {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }

    /**
     * Returns an iterable of page numbers useful for pagination UI.
     * Keeps a sliding window of $delta pages around the current page.
     *
     * @return int[]
     */
    public function getPageRange(int $delta = 2): array
    {
        $total = $this->getTotalPages();
        $start = max(1, $this->currentPage - $delta);
        $end   = min($total, $this->currentPage + $delta);

        return range($start, $end);
    }
}
