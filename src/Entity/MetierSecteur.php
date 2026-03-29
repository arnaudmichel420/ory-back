<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MetierSecteurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MetierSecteurRepository::class)]
class MetierSecteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'metierSecteurs')]
    #[ORM\JoinColumn(referencedColumnName: 'code_ogr', nullable: false)]
    private ?Metier $codeOgrMetier = null;

    #[ORM\ManyToOne(inversedBy: 'metierSecteurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Secteur $secteur = null;

    #[ORM\Column(nullable: true)]
    private ?bool $principal = null;

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

    public function getSecteur(): ?Secteur
    {
        return $this->secteur;
    }

    public function setSecteur(?Secteur $secteur): static
    {
        $this->secteur = $secteur;

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
