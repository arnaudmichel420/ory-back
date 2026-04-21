<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\MetierCompetenceTypeEnum;
use App\Repository\MetierCompetenceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MetierCompetenceRepository::class)]
class MetierCompetence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['metier:view'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'metierCompetences')]
    #[ORM\JoinColumn(referencedColumnName: 'code_ogr', nullable: false)]
    private ?Metier $codeOgrMetier = null;

    #[ORM\ManyToOne(inversedBy: 'metierCompetences')]
    #[ORM\JoinColumn(referencedColumnName: 'code_ogr', nullable: false)]
    #[Groups(['metier:view'])]
    private ?Competence $codeOgrComp = null;

    #[ORM\Column(enumType: MetierCompetenceTypeEnum::class)]
    #[Groups(['metier:view'])]
    private ?MetierCompetenceTypeEnum $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['metier:view'])]
    private ?string $libelleEnjeu = null;

    #[ORM\Column(options: ['default' => 0])]
    #[Groups(['metier:view'])]
    private ?int $coeurMetier = 0;

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

    public function getCodeOgrComp(): ?Competence
    {
        return $this->codeOgrComp;
    }

    public function setCodeOgrComp(?Competence $codeOgrComp): static
    {
        $this->codeOgrComp = $codeOgrComp;

        return $this;
    }

    public function getType(): ?MetierCompetenceTypeEnum
    {
        return $this->type;
    }

    public function setType(MetierCompetenceTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getLibelleEnjeu(): ?string
    {
        return $this->libelleEnjeu;
    }

    public function setLibelleEnjeu(?string $libelleEnjeu): static
    {
        $this->libelleEnjeu = $libelleEnjeu;

        return $this;
    }

    public function getCoeurMetier(): ?int
    {
        return $this->coeurMetier;
    }

    public function setCoeurMetier(int $coeurMetier): static
    {
        $this->coeurMetier = $coeurMetier;

        return $this;
    }
}
