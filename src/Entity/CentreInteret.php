<?php

namespace App\Entity;

use App\Repository\CentreInteretRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CentreInteretRepository::class)]
class CentreInteret
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $definition = null;

    /**
     * @var Collection<int, MetierCentreInteret>
     */
    #[ORM\OneToMany(targetEntity: MetierCentreInteret::class, mappedBy: 'centreInteret')]
    private Collection $metierCentreInterets;

    /**
     * @var Collection<int, ChoixReco>
     */
    #[ORM\OneToMany(targetEntity: ChoixReco::class, mappedBy: 'centreInteret')]
    private Collection $choixRecos;

    public function __construct()
    {
        $this->metierCentreInterets = new ArrayCollection();
        $this->choixRecos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDefinition(): ?string
    {
        return $this->definition;
    }

    public function setDefinition(?string $definition): static
    {
        $this->definition = $definition;

        return $this;
    }

    /**
     * @return Collection<int, MetierCentreInteret>
     */
    public function getMetierCentreInterets(): Collection
    {
        return $this->metierCentreInterets;
    }

    public function addMetierCentreInteret(MetierCentreInteret $metierCentreInteret): static
    {
        if (!$this->metierCentreInterets->contains($metierCentreInteret)) {
            $this->metierCentreInterets->add($metierCentreInteret);
            $metierCentreInteret->setCentreInteret($this);
        }

        return $this;
    }

    public function removeMetierCentreInteret(MetierCentreInteret $metierCentreInteret): static
    {
        if ($this->metierCentreInterets->removeElement($metierCentreInteret)) {
            // set the owning side to null (unless already changed)
            if ($metierCentreInteret->getCentreInteret() === $this) {
                $metierCentreInteret->setCentreInteret(null);
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
            $choixReco->setCentreInteret($this);
        }

        return $this;
    }

    public function removeChoixReco(ChoixReco $choixReco): static
    {
        if ($this->choixRecos->removeElement($choixReco)) {
            // set the owning side to null (unless already changed)
            if ($choixReco->getCentreInteret() === $this) {
                $choixReco->setCentreInteret(null);
            }
        }

        return $this;
    }
}
