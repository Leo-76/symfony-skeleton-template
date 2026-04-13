<?php

declare(strict_types=1);

namespace App\Tests\Unit\Pagination;

use App\Pagination\PaginatedResult;
use PHPUnit\Framework\TestCase;

final class PaginatedResultTest extends TestCase
{
    public function testTotalPages(): void
    {
        $result = new PaginatedResult([], 100, 1, 20);
        $this->assertSame(5, $result->getTotalPages());
    }

    public function testTotalPagesRoundsUp(): void
    {
        $result = new PaginatedResult([], 21, 1, 20);
        $this->assertSame(2, $result->getTotalPages());
    }

    public function testHasPreviousPage(): void
    {
        $this->assertFalse((new PaginatedResult([], 50, 1, 10))->hasPreviousPage());
        $this->assertTrue((new PaginatedResult([], 50, 2, 10))->hasPreviousPage());
    }

    public function testHasNextPage(): void
    {
        $this->assertFalse((new PaginatedResult([], 10, 1, 10))->hasNextPage());
        $this->assertTrue((new PaginatedResult([], 20, 1, 10))->hasNextPage());
    }

    public function testGetOffset(): void
    {
        $this->assertSame(0,  (new PaginatedResult([], 100, 1, 20))->getOffset());
        $this->assertSame(20, (new PaginatedResult([], 100, 2, 20))->getOffset());
        $this->assertSame(40, (new PaginatedResult([], 100, 3, 20))->getOffset());
    }

    public function testGetPageRange(): void
    {
        $result = new PaginatedResult([], 100, 5, 10); // 10 pages, current = 5
        $range  = $result->getPageRange(2);
        $this->assertSame([3, 4, 5, 6, 7], $range);
    }

    public function testGetPageRangeClampedAtEdges(): void
    {
        $result = new PaginatedResult([], 100, 1, 10);
        $range  = $result->getPageRange(2);
        $this->assertSame([1, 2, 3], $range);
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue((new PaginatedResult([], 0, 1, 10))->isEmpty());
        $this->assertFalse((new PaginatedResult([1], 1, 1, 10))->isEmpty());
    }
}
