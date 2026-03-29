<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\TimestampableTrait;
use App\Enum\EtudiantMetierInteractionTypeEnum;
use App\Repository\EtudiantMetierInteractionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtudiantMetierInteractionRepository::class)]
class EtudiantMetierInteraction
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'etudiantMetierInteractions')]
    #[ORM\JoinColumn(referencedColumnName: 'code_ogr', nullable: false)]
    private ?Metier $codeOgrMetier = null;

    #[ORM\ManyToOne(inversedBy: 'etudiantMetierInteractions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etudiant $etudiant = null;

    #[ORM\Column(enumType: EtudiantMetierInteractionTypeEnum::class)]
    private ?EtudiantMetierInteractionTypeEnum $type = null;

    #[ORM\Column]
    private ?int $poids = null;

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

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): static
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getType(): ?EtudiantMetierInteractionTypeEnum
    {
        return $this->type;
    }

    public function setType(EtudiantMetierInteractionTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPoids(): ?int
    {
        return $this->poids;
    }

    public function setPoids(int $poids): static
    {
        $this->poids = $poids;

        return $this;
    }
}
