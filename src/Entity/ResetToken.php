<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\HasUuid;
use App\Repository\ResetTokenRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResetTokenRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ResetToken
{
    use HasUuid;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(length: 64, unique: true)]
    private string $token;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $expiresAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $used = false;

    public function __construct(User $user, int $ttlSeconds = 3600)
    {
        $this->initId();
        $this->user      = $user;
        $this->token     = bin2hex(random_bytes(32));
        $this->expiresAt = new \DateTimeImmutable(sprintf('+%d seconds', $ttlSeconds));
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function markAsUsed(): void
    {
        $this->used = true;
    }

    public function isValid(): bool
    {
        return !$this->used && !$this->isExpired();
    }
}
