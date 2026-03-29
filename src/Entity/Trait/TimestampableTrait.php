<?php

declare(strict_types=1);

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait TimestampableTrait
{
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $creeLe = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $modifieLe = null;

    public function getCreeLe(): ?\DateTimeImmutable
    {
        return $this->creeLe;
    }

    public function getModifieLe(): ?\DateTimeImmutable
    {
        return $this->modifieLe;
    }
}
