<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MetierCentreInteretRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MetierCentreInteretRepository::class)]
class MetierCentreInteret
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'metierCentreInterets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CentreInteret $centreInteret = null;

    #[ORM\ManyToOne(inversedBy: 'metierCentreInterets')]
    #[ORM\JoinColumn(referencedColumnName: 'code_ogr', nullable: false)]
    private ?Metier $codeOgrMetier = null;

    #[ORM\Column(nullable: true)]
    private ?bool $principal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCentreInteret(): ?CentreInteret
    {
        return $this->centreInteret;
    }

    public function setCentreInteret(?CentreInteret $centreInteret): static
    {
        $this->centreInteret = $centreInteret;

        return $this;
    }

    public function getCodeOgrMetier(): ?Metier
    {
        return $this->codeOgrMetier;
    }

    public function setCodeOgrMetier(?Metier $codeOgrMetier): static
    {
        $this->codeOgrMetier = $codeOgrMetier;

        return $this;
    }

    public function isPrincipal(): ?bool
    {
        return $this->principal;
    }

    public function setPrincipal(?bool $principal): static
    {
        $this->principal = $principal;

        return $this;
    }
}
