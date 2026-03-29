<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\TimestampableTrait;
use App\Repository\EtudiantReponseRecoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtudiantReponseRecoRepository::class)]
class EtudiantReponseReco
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'etudiantReponseRecos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etudiant $etudiant = null;

    #[ORM\ManyToOne(inversedBy: 'etudiantReponseRecos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ChoixReco $choix = null;

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

    public function getChoix(): ?ChoixReco
    {
        return $this->choix;
    }

    public function setChoix(?ChoixReco $choix): static
    {
        $this->choix = $choix;

        return $this;
    }
}
