<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ContexteTravailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContexteTravailRepository::class)]
class ContexteTravail
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private ?string $codeOgr = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeContexte = null;

    /**
     * @var Collection<int, MetierContexteTravail>
     */
    #[ORM\OneToMany(targetEntity: MetierContexteTravail::class, mappedBy: 'codeOgrContexte')]
    private Collection $metierContexteTravails;

    /**
     * @var Collection<int, ChoixReco>
     */
    #[ORM\OneToMany(targetEntity: ChoixReco::class, mappedBy: 'contexteTravail')]
    private Collection $choixRecos;

    public function __construct()
    {
        $this->metierContexteTravails = new ArrayCollection();
        $this->choixRecos = new ArrayCollection();
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

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getTypeContexte(): ?string
    {
        return $this->typeContexte;
    }

    public function setTypeContexte(?string $typeContexte): static
    {
        $this->typeContexte = $typeContexte;

        return $this;
    }

    /**
     * @return Collection<int, MetierContexteTravail>
     */
    public function getMetierContexteTravails(): Collection
    {
        return $this->metierContexteTravails;
    }

    public function addMetierContexteTravail(MetierContexteTravail $metierContexteTravail): static
    {
        if (!$this->metierContexteTravails->contains($metierContexteTravail)) {
            $this->metierContexteTravails->add($metierContexteTravail);
            $metierContexteTravail->setCodeOgrContexte($this);
        }

        return $this;
    }

    public function removeMetierContexteTravail(MetierContexteTravail $metierContexteTravail): static
    {
        if ($this->metierContexteTravails->removeElement($metierContexteTravail)) {
            // set the owning side to null (unless already changed)
            if ($metierContexteTravail->getCodeOgrContexte() === $this) {
                $metierContexteTravail->setCodeOgrContexte(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ChoixReco>
     */
    public function getChoixRecos(): Collection
    {
        return $this->choixRecos;
    }

    public function addChoixReco(ChoixReco $choixReco): static
    {
        if (!$this->choixRecos->contains($choixReco)) {
            $this->choixRecos->add($choixReco);
            $choixReco->setContexteTravail($this);
        }

        return $this;
    }

    public function removeChoixReco(ChoixReco $choixReco): static
    {
        if ($this->choixRecos->removeElement($choixReco)) {
            // set the owning side to null (unless already changed)
            if ($choixReco->getContexteTravail() === $this) {
                $choixReco->setContexteTravail(null);
            }
        }

        return $this;
    }
}
