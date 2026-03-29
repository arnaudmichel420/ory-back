<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AppellationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppellationRepository::class)]
class Appellation
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private ?string $codeOgr = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelleCourt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $peuUtiliser = null;

    #[ORM\ManyToOne(inversedBy: 'appellations')]
    #[ORM\JoinColumn(referencedColumnName: 'code_ogr', nullable: false)]
    private ?Metier $codeOgrMetier = null;

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getCodeOgr(): ?string
    {
        return $this->codeOgr;
    }

    public function setCodeOgr(string $codeOgr): static
    {
        $this->codeOgr = $codeOgr;

        return $this;
    }

    public function getLibelleCourt(): ?string
    {
        return $this->libelleCourt;
    }

    public function setLibelleCourt(?string $libelleCourt): static
    {
        $this->libelleCourt = $libelleCourt;

        return $this;
    }

    public function isPeuUtiliser(): ?bool
    {
        return $this->peuUtiliser;
    }

    public function setPeuUtiliser(bool $peuUtiliser): static
    {
        $this->peuUtiliser = $peuUtiliser;

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
}
