<?php

namespace App\Entity;

use App\Repository\MobiliteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MobiliteRepository::class)]
class Mobilite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'mobilites')]
    #[ORM\JoinColumn(referencedColumnName: 'code_ogr', nullable: false)]
    private ?Metier $codeOgrMetierSource = null;

    #[ORM\Column(nullable: true)]
    private ?int $ordreMobilite = null;

    #[ORM\Column(length: 255)]
    private ?string $codeOgrMetierCible = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeOgrMetierSource(): ?Metier
    {
        return $this->codeOgrMetierSource;
    }

    public function setCodeOgrMetierSource(?Metier $codeOgrMetierSource): static
    {
        $this->codeOgrMetierSource = $codeOgrMetierSource;

        return $this;
    }

    public function getOrdreMobilite(): ?int
    {
        return $this->ordreMobilite;
    }

    public function setOrdreMobilite(?int $ordreMobilite): static
    {
        $this->ordreMobilite = $ordreMobilite;

        return $this;
    }

    public function getCodeOgrMetierCible(): ?string
    {
        return $this->codeOgrMetierCible;
    }

    public function setCodeOgrMetierCible(string $codeOgrMetierCible): static
    {
        $this->codeOgrMetierCible = $codeOgrMetierCible;

        return $this;
    }
}
