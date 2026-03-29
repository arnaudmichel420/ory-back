<?php

declare(strict_types=1);

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait SoftDeleteableTrait
{
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $supprimeLe = null;

    public function getSupprimeLe(): ?\DateTimeImmutable
    {
        return $this->supprimeLe;
    }

    public function setSupprimeLe(?\DateTimeImmutable $supprimeLe): static
    {
        $this->supprimeLe = $supprimeLe;

        return $this;
    }

    public function isDeleted(): bool
    {
        return null !== $this->supprimeLe;
    }
}
