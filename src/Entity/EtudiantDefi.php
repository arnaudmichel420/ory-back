<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\TimestampableTrait;
use App\Enum\EtudiantDefiStatutEnum;
use App\Repository\EtudiantDefiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtudiantDefiRepository::class)]
class EtudiantDefi
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'etudiantDefis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etudiant $etudiant = null;

    #[ORM\ManyToOne(inversedBy: 'etudiantDefis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Defi $defi = null;

    #[ORM\Column(enumType: EtudiantDefiStatutEnum::class)]
    private ?EtudiantDefiStatutEnum $statut = null;

    #[ORM\Column(nullable: true)]
    private ?int $progression = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completeLe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): static
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getDefi(): ?Defi
    {
        return $this->defi;
    }

    public function setDefi(?Defi $defi): static
    {
        $this->defi = $defi;

        return $this;
    }

    public function getStatut(): ?EtudiantDefiStatutEnum
    {
        return $this->statut;
    }

    public function setStatut(EtudiantDefiStatutEnum $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getProgression(): ?int
    {
        return $this->progression;
    }

    public function setProgression(?int $progression): static
    {
        $this->progression = $progression;

        return $this;
    }

    public function getCompleteLe(): ?\DateTimeImmutable
    {
        return $this->completeLe;
    }

    public function setCompleteLe(?\DateTimeImmutable $completeLe): static
    {
        $this->completeLe = $completeLe;

        return $this;
    }
}
