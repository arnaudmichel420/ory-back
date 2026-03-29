<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DomaineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DomaineRepository::class)]
class Domaine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    /**
     * @var Collection<int, SousDomaine>
     */
    #[ORM\OneToMany(targetEntity: SousDomaine::class, mappedBy: 'domaine')]
    private Collection $sousDomaines;

    public function __construct()
    {
        $this->sousDomaines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, SousDomaine>
     */
    public function getSousDomaines(): Collection
    {
        return $this->sousDomaines;
    }

    public function addSousDomaine(SousDomaine $sousDomaine): static
    {
        if (!$this->sousDomaines->contains($sousDomaine)) {
            $this->sousDomaines->add($sousDomaine);
            $sousDomaine->setDomaine($this);
        }

        return $this;
    }

    public function removeSousDomaine(SousDomaine $sousDomaine): static
    {
        if ($this->sousDomaines->removeElement($sousDomaine)) {
            // set the owning side to null (unless already changed)
            if ($sousDomaine->getDomaine() === $this) {
                $sousDomaine->setDomaine(null);
            }
        }

        return $this;
    }
}
