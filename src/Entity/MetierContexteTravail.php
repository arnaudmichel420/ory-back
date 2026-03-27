<?php

namespace App\Entity;

use App\Repository\MetierContexteTravailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MetierContexteTravailRepository::class)]
class MetierContexteTravail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'metierContexteTravails')]
    #[ORM\JoinColumn(referencedColumnName: 'code_ogr', nullable: false)]
    private ?Metier $codeOgrMetier = null;

    #[ORM\ManyToOne(inversedBy: 'metierContexteTravails')]
    #[ORM\JoinColumn(referencedColumnName: 'code_ogr', nullable: false)]
    private ?ContexteTravail $codeOgrContexte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelleGroupe = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCodeOgrContexte(): ?ContexteTravail
    {
        return $this->codeOgrContexte;
    }

    public function setCodeOgrContexte(?ContexteTravail $codeOgrContexte): static
    {
        $this->codeOgrContexte = $codeOgrContexte;

        return $this;
    }

    public function getLibelleGroupe(): ?string
    {
        return $this->libelleGroupe;
    }

    public function setLibelleGroupe(?string $libelleGroupe): static
    {
        $this->libelleGroupe = $libelleGroupe;

        return $this;
    }
}
