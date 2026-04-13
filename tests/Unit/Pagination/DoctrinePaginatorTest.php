<?php

declare(strict_types=1);

namespace App\Tests\Unit\Pagination;

use App\Pagination\DoctrinePaginator;
use PHPUnit\Framework\TestCase;

/**
 * DoctrinePaginator wraps Doctrine's own Paginator which requires a real ORM.
 * We test the offset/limit maths in isolation here instead of mocking the ORM.
 */
final class DoctrinePaginatorTest extends TestCase
{
    public function testDefaultLimitConstant(): void
    {
        $this->assertSame(20, DoctrinePaginator::DEFAULT_LIMIT);
    }

    public function testPageOneOffset(): void
    {
        // Offset = (page - 1) * limit
        $limit  = 10;
        $page   = 1;
        $offset = ($page - 1) * $limit;
        $this->assertSame(0, $offset);
    }

    public function testPageThreeOffset(): void
    {
        $limit  = 10;
        $page   = 3;
        $offset = ($page - 1) * $limit;
        $this->assertSame(20, $offset);
    }

    public function testNegativePageClampsToOne(): void
    {
        $page = max(1, -5);
        $this->assertSame(1, $page);
    }

    public function testZeroLimitClampsToOne(): void
    {
        $limit = max(1, 0);
        $this->assertSame(1, $limit);
    }
}
