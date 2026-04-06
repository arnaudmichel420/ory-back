<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\TerritoireCodeTypeTerritoireEnum;
use App\Repository\TerritoireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TerritoireRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_territoire_type_code', columns: ['code_type_territoire', 'code_territoire'])]
class Territoire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true, enumType: TerritoireCodeTypeTerritoireEnum::class)]
    private ?TerritoireCodeTypeTerritoireEnum $codeTypeTerritoire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeTerritoire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelleTerritoire = null;

    #[ORM\Column(nullable: true, enumType: TerritoireCodeTypeTerritoireEnum::class)]
    private ?TerritoireCodeTypeTerritoireEnum $codeTypeTerritoireParent = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'territoires')]
    #[ORM\JoinColumn(nullable: true)]
    private ?self $codeTerritoireParent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'codeTerritoireParent')]
    private Collection $territoires;

    /**
     * @var Collection<int, MetierAttractivite>
     */
    #[ORM\OneToMany(targetEntity: MetierAttractivite::class, mappedBy: 'territoire')]
    private Collection $metierAttractivites;

    public function __construct()
    {
        $this->territoires = new ArrayCollection();
        $this->metierAttractivites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeTypeTerritoire(): ?TerritoireCodeTypeTerritoireEnum
    {
        return $this->codeTypeTerritoire;
    }

    public function setCodeTypeTerritoire(TerritoireCodeTypeTerritoireEnum $codeTypeTerritoire): static
    {
        $this->codeTypeTerritoire = $codeTypeTerritoire;

        return $this;
    }

    public function getCodeTerritoire(): ?string
    {
        return $this->codeTerritoire;
    }

    public function setCodeTerritoire(string $codeTerritoire): static
    {
        $this->codeTerritoire = $codeTerritoire;

        return $this;
    }

    public function getLibelleTerritoire(): ?string
    {
        return $this->libelleTerritoire;
    }

    public function setLibelleTerritoire(string $libelleTerritoire): static
    {
        $this->libelleTerritoire = $libelleTerritoire;

        return $this;
    }

    public function getCodeTypeTerritoireParent(): ?TerritoireCodeTypeTerritoireEnum
    {
        return $this->codeTypeTerritoireParent;
    }

    public function setCodeTypeTerritoireParent(?TerritoireCodeTypeTerritoireEnum $codeTypeTerritoireParent): static
    {
        $this->codeTypeTerritoireParent = $codeTypeTerritoireParent;

        return $this;
    }

    public function getCodeTerritoireParent(): ?self
    {
        return $this->codeTerritoireParent;
    }

    public function setCodeTerritoireParent(?self $codeTerritoireParent): static
    {
        $this->codeTerritoireParent = $codeTerritoireParent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getTerritoires(): Collection
    {
        return $this->territoires;
    }

    public function addTerritoire(self $territoire): static
    {
        if (!$this->territoires->contains($territoire)) {
            $this->territoires->add($territoire);
            $territoire->setCodeTerritoireParent($this);
        }

        return $this;
    }

    public function removeTerritoire(self $territoire): static
    {
        if ($this->territoires->removeElement($territoire)) {
            // set the owning side to null (unless already changed)
            if ($territoire->getCodeTerritoireParent() === $this) {
                $territoire->setCodeTerritoireParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MetierAttractivite>
     */
    public function getMetierAttractivites(): Collection
    {
        return $this->metierAttractivites;
    }

    public function addMetierAttractivite(MetierAttractivite $metierAttractivite): static
    {
        if (!$this->metierAttractivites->contains($metierAttractivite)) {
            $this->metierAttractivites->add($metierAttractivite);
            $metierAttractivite->setTerritoire($this);
        }

        return $this;
    }

    public function removeMetierAttractivite(MetierAttractivite $metierAttractivite): static
    {
        if ($this->metierAttractivites->removeElement($metierAttractivite)) {
            // set the owning side to null (unless already changed)
            if ($metierAttractivite->getTerritoire() === $this) {
                $metierAttractivite->setTerritoire(null);
            }
        }

        return $this;
    }
}
