<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\MetierAttractiviteCodeEnum;
use App\Repository\MetierAttractiviteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MetierAttractiviteRepository::class)]
#[ORM\Table(name: 'metier_attractivite', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uniq_metier_attractivite_lookup', columns: ['code_ogr_metier_id', 'territoire_id', 'code_attractivite'])])]
class MetierAttractivite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'metierAttractivites')]
    #[ORM\JoinColumn(referencedColumnName: 'code_ogr', nullable: false)]
    private ?Metier $codeOgrMetier = null;

    #[ORM\Column(length: 32, enumType: MetierAttractiviteCodeEnum::class)]
    private ?MetierAttractiviteCodeEnum $codeAttractivite = null;

    #[ORM\ManyToOne(inversedBy: 'metierAttractivites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Territoire $territoire = null;

    #[ORM\Column]
    private ?int $valeur = null;

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

    public function getCodeAttractivite(): ?MetierAttractiviteCodeEnum
    {
        return $this->codeAttractivite;
    }

    public function setCodeAttractivite(MetierAttractiviteCodeEnum $codeAttractivite): static
    {
        $this->codeAttractivite = $codeAttractivite;

        return $this;
    }

    public function getTerritoire(): ?Territoire
    {
        return $this->territoire;
    }

    public function setTerritoire(?Territoire $territoire): static
    {
        $this->territoire = $territoire;

        return $this;
    }

    public function getValeur(): ?int
    {
        return $this->valeur;
    }

    public function setValeur(int $valeur): static
    {
        $this->valeur = $valeur;

        return $this;
    }
}
