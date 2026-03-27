<?php

namespace App\Entity;

use App\Repository\SecteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SecteurRepository::class)]
class Secteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $definition = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'secteurs')]
    private ?self $sousSecteurParent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'sousSecteurParent')]
    private Collection $secteurs;

    /**
     * @var Collection<int, MetierSecteur>
     */
    #[ORM\OneToMany(targetEntity: MetierSecteur::class, mappedBy: 'secteur')]
    private Collection $metierSecteurs;

    /**
     * @var Collection<int, ChoixReco>
     */
    #[ORM\OneToMany(targetEntity: ChoixReco::class, mappedBy: 'secteur')]
    private Collection $choixRecos;

    public function __construct()
    {
        $this->secteurs = new ArrayCollection();
        $this->metierSecteurs = new ArrayCollection();
        $this->choixRecos = new ArrayCollection();
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

    public function getDefinition(): ?string
    {
        return $this->definition;
    }

    public function setDefinition(string $definition): static
    {
        $this->definition = $definition;

        return $this;
    }

    public function getSousSecteurParent(): ?self
    {
        return $this->sousSecteurParent;
    }

    public function setSousSecteurParent(?self $sousSecteurParent): static
    {
        $this->sousSecteurParent = $sousSecteurParent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getSecteurs(): Collection
    {
        return $this->secteurs;
    }

    public function addSecteur(self $secteur): static
    {
        if (!$this->secteurs->contains($secteur)) {
            $this->secteurs->add($secteur);
            $secteur->setSousSecteurParent($this);
        }

        return $this;
    }

    public function removeSecteur(self $secteur): static
    {
        if ($this->secteurs->removeElement($secteur)) {
            // set the owning side to null (unless already changed)
            if ($secteur->getSousSecteurParent() === $this) {
                $secteur->setSousSecteurParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MetierSecteur>
     */
    public function getMetierSecteurs(): Collection
    {
        return $this->metierSecteurs;
    }

    public function addMetierSecteur(MetierSecteur $metierSecteur): static
    {
        if (!$this->metierSecteurs->contains($metierSecteur)) {
            $this->metierSecteurs->add($metierSecteur);
            $metierSecteur->setSecteur($this);
        }

        return $this;
    }

    public function removeMetierSecteur(MetierSecteur $metierSecteur): static
    {
        if ($this->metierSecteurs->removeElement($metierSecteur)) {
            // set the owning side to null (unless already changed)
            if ($metierSecteur->getSecteur() === $this) {
                $metierSecteur->setSecteur(null);
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
            $choixReco->setSecteur($this);
        }

        return $this;
    }

    public function removeChoixReco(ChoixReco $choixReco): static
    {
        if ($this->choixRecos->removeElement($choixReco)) {
            // set the owning side to null (unless already changed)
            if ($choixReco->getSecteur() === $this) {
                $choixReco->setSecteur(null);
            }
        }

        return $this;
    }
}
