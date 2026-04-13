<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\ResetToken;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class ResetTokenTest extends TestCase
{
    private function makeUser(): User
    {
        $u = new User();
        $u->setEmail('t@t.com')->setFullName('T')->setPassword('x');
        return $u;
    }

    public function testTokenIsValidByDefault(): void
    {
        $token = new ResetToken($this->makeUser());
        $this->assertTrue($token->isValid());
        $this->assertFalse($token->isExpired());
        $this->assertFalse($token->isUsed());
    }

    public function testMarkAsUsedInvalidatesToken(): void
    {
        $token = new ResetToken($this->makeUser());
        $token->markAsUsed();
        $this->assertTrue($token->isUsed());
        $this->assertFalse($token->isValid());
    }

    public function testExpiredTokenIsInvalid(): void
    {
        $token = new ResetToken($this->makeUser(), ttlSeconds: -1);
        $this->assertTrue($token->isExpired());
        $this->assertFalse($token->isValid());
    }

    public function testTokenHas64HexChars(): void
    {
        $token = new ResetToken($this->makeUser());
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $token->getToken());
    }

    public function testDefaultTtlIsOneHour(): void
    {
        $token     = new ResetToken($this->makeUser());
        $remaining = $token->getExpiresAt()->getTimestamp() - time();
        $this->assertGreaterThan(3500, $remaining);
        $this->assertLessThanOrEqual(3600, $remaining);
    }
}
