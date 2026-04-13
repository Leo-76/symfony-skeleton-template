<?php

declare(strict_types=1);

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Provides a UUID v7 primary key.
 * Use on any Doctrine entity that needs a UUID identifier.
 */
trait HasUuid
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    public function getId(): Uuid
    {
        return $this->id;
    }

    protected function initId(): void
    {
        $this->id = Uuid::v7();
    }
}
