<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ResetToken;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<ResetToken>
 */
class ResetTokenRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetToken::class);
    }

    public function findValidToken(string $token): ?ResetToken
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.token = :token')
            ->andWhere('t.used = false')
            ->andWhere('t.expiresAt > :now')
            ->setParameter('token', $token)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function invalidateUserTokens(User $user): void
    {
        $this->createQueryBuilder('t')
            ->update()
            ->set('t.used', 'true')
            ->andWhere('t.user = :user')
            ->andWhere('t.used = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    public function purgeExpired(): int
    {
        return (int) $this->createQueryBuilder('t')
            ->delete()
            ->andWhere('t.expiresAt < :now OR t.used = true')
            ->setParameter('now', new \DateTimeImmutable('-1 day'))
            ->getQuery()
            ->execute();
    }
}
