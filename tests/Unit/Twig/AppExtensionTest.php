<?php

declare(strict_types=1);

namespace App\Tests\Unit\Twig;

use App\Twig\AppExtension;
use PHPUnit\Framework\TestCase;

final class AppExtensionTest extends TestCase
{
    private AppExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new AppExtension();
    }

    public function testTruncateShortString(): void
    {
        $this->assertSame('Hello', $this->extension->truncate('Hello', 10));
    }

    public function testTruncateLongString(): void
    {
        $result = $this->extension->truncate('Hello World', 5);
        $this->assertSame('Hello…', $result);
    }

    public function testHumanFilesizeBytes(): void
    {
        $this->assertSame('512 B', $this->extension->humanFilesize(512));
    }

    public function testHumanFilesizeKilobytes(): void
    {
        $this->assertSame('1 KB', $this->extension->humanFilesize(1024));
    }

    public function testHumanFilesizeMegabytes(): void
    {
        $this->assertSame('1.5 MB', $this->extension->humanFilesize((int)(1.5 * 1024 * 1024)));
    }

    public function testInitialsTwoWords(): void
    {
        $this->assertSame('JD', $this->extension->initials('John Doe'));
    }

    public function testInitialsThreeWords(): void
    {
        $this->assertSame('JD', $this->extension->initials('Jean de Dupont', 2));
    }

    public function testInitialsSingleWord(): void
    {
        $this->assertSame('A', $this->extension->initials('Alice'));
    }
}
