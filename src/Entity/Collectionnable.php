<?php

namespace App\Entity;

use App\Entity\Trait\SoftDeleteableTrait;
use App\Repository\CollectionnableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: CollectionnableRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'supprimeLe', timeAware: false, hardDelete: false)]
class Collectionnable
{
    use SoftDeleteableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $libelle = null;

    #[ORM\Column(length: 100)]
    private ?string $valeur = null;

    /**
     * @var Collection<int, Defi>
     */
    #[ORM\ManyToMany(targetEntity: Defi::class, inversedBy: 'collectionnables')]
    private Collection $defi;

    /**
     * @var Collection<int, Etudiant>
     */
    #[ORM\ManyToMany(targetEntity: Etudiant::class, mappedBy: 'collectionnable')]
    private Collection $etudiants;

    public function __construct()
    {
        $this->defi = new ArrayCollection();
        $this->etudiants = new ArrayCollection();
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

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(string $valeur): static
    {
        $this->valeur = $valeur;

        return $this;
    }

    /**
     * @return Collection<int, Defi>
     */
    public function getDefi(): Collection
    {
        return $this->defi;
    }

    public function addDefi(Defi $defi): static
    {
        if (!$this->defi->contains($defi)) {
            $this->defi->add($defi);
        }

        return $this;
    }

    public function removeDefi(Defi $defi): static
    {
        $this->defi->removeElement($defi);

        return $this;
    }

    /**
     * @return Collection<int, Etudiant>
     */
    public function getEtudiants(): Collection
    {
        return $this->etudiants;
    }

    public function addEtudiant(Etudiant $etudiant): static
    {
        if (!$this->etudiants->contains($etudiant)) {
            $this->etudiants->add($etudiant);
            $etudiant->addCollectionnable($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): static
    {
        if ($this->etudiants->removeElement($etudiant)) {
            $etudiant->removeCollectionnable($this);
        }

        return $this;
    }
}
