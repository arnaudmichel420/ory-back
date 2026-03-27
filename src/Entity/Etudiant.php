<?php

namespace App\Entity;

use App\Entity\Trait\TimestampableTrait;
use App\Repository\EtudiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtudiantRepository::class)]
class Etudiant
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'etudiant', cascade: ['persist', 'remove'])]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(length: 10)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 20)]
    private ?string $telephone = null;

    /**
     * @var Collection<int, EtudiantDefi>
     */
    #[ORM\OneToMany(targetEntity: EtudiantDefi::class, mappedBy: 'etudiant')]
    private Collection $etudiantDefis;

    /**
     * @var Collection<int, Collectionnable>
     */
    #[ORM\ManyToMany(targetEntity: Collectionnable::class, inversedBy: 'etudiants')]
    private Collection $collectionnable;

    /**
     * @var Collection<int, Metier>
     */
    #[ORM\ManyToMany(targetEntity: Metier::class, mappedBy: 'etudiant')]
    private Collection $favori;

    /**
     * @var Collection<int, EtudiantMetierInteraction>
     */
    #[ORM\OneToMany(targetEntity: EtudiantMetierInteraction::class, mappedBy: 'etudiant', orphanRemoval: true)]
    private Collection $etudiantMetierInteractions;

    /**
     * @var Collection<int, EtudiantMetierScore>
     */
    #[ORM\OneToMany(targetEntity: EtudiantMetierScore::class, mappedBy: 'etudiant', orphanRemoval: true)]
    private Collection $etudiantMetierScores;

    /**
     * @var Collection<int, EtudiantReponseReco>
     */
    #[ORM\OneToMany(targetEntity: EtudiantReponseReco::class, mappedBy: 'etudiant', orphanRemoval: true)]
    private Collection $etudiantReponseRecos;

    public function __construct()
    {
        $this->etudiantDefis = new ArrayCollection();
        $this->collectionnable = new ArrayCollection();
        $this->favori = new ArrayCollection();
        $this->etudiantMetierInteractions = new ArrayCollection();
        $this->etudiantMetierScores = new ArrayCollection();
        $this->etudiantReponseRecos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return Collection<int, EtudiantDefi>
     */
    public function getEtudiantDefis(): Collection
    {
        return $this->etudiantDefis;
    }

    public function addEtudiantDefi(EtudiantDefi $etudiantDefi): static
    {
        if (!$this->etudiantDefis->contains($etudiantDefi)) {
            $this->etudiantDefis->add($etudiantDefi);
            $etudiantDefi->setEtudiant($this);
        }

        return $this;
    }

    public function removeEtudiantDefi(EtudiantDefi $etudiantDefi): static
    {
        if ($this->etudiantDefis->removeElement($etudiantDefi)) {
            // set the owning side to null (unless already changed)
            if ($etudiantDefi->getEtudiant() === $this) {
                $etudiantDefi->setEtudiant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Collectionnable>
     */
    public function getCollectionnable(): Collection
    {
        return $this->collectionnable;
    }

    public function addCollectionnable(Collectionnable $collectionnable): static
    {
        if (!$this->collectionnable->contains($collectionnable)) {
            $this->collectionnable->add($collectionnable);
        }

        return $this;
    }

    public function removeCollectionnable(Collectionnable $collectionnable): static
    {
        $this->collectionnable->removeElement($collectionnable);

        return $this;
    }

    /**
     * @return Collection<int, Metier>
     */
    public function getFavori(): Collection
    {
        return $this->favori;
    }

    public function addFavori(Metier $favori): static
    {
        if (!$this->favori->contains($favori)) {
            $this->favori->add($favori);
            $favori->addEtudiant($this);
        }

        return $this;
    }

    public function removeFavori(Metier $favori): static
    {
        if ($this->favori->removeElement($favori)) {
            $favori->removeEtudiant($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, EtudiantMetierInteraction>
     */
    public function getEtudiantMetierInteractions(): Collection
    {
        return $this->etudiantMetierInteractions;
    }

    public function addEtudiantMetierInteraction(EtudiantMetierInteraction $etudiantMetierInteraction): static
    {
        if (!$this->etudiantMetierInteractions->contains($etudiantMetierInteraction)) {
            $this->etudiantMetierInteractions->add($etudiantMetierInteraction);
            $etudiantMetierInteraction->setEtudiant($this);
        }

        return $this;
    }

    public function removeEtudiantMetierInteraction(EtudiantMetierInteraction $etudiantMetierInteraction): static
    {
        if ($this->etudiantMetierInteractions->removeElement($etudiantMetierInteraction)) {
            // set the owning side to null (unless already changed)
            if ($etudiantMetierInteraction->getEtudiant() === $this) {
                $etudiantMetierInteraction->setEtudiant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EtudiantMetierScore>
     */
    public function getEtudiantMetierScores(): Collection
    {
        return $this->etudiantMetierScores;
    }

    public function addEtudiantMetierScore(EtudiantMetierScore $etudiantMetierScore): static
    {
        if (!$this->etudiantMetierScores->contains($etudiantMetierScore)) {
            $this->etudiantMetierScores->add($etudiantMetierScore);
            $etudiantMetierScore->setEtudiant($this);
        }

        return $this;
    }

    public function removeEtudiantMetierScore(EtudiantMetierScore $etudiantMetierScore): static
    {
        if ($this->etudiantMetierScores->removeElement($etudiantMetierScore)) {
            // set the owning side to null (unless already changed)
            if ($etudiantMetierScore->getEtudiant() === $this) {
                $etudiantMetierScore->setEtudiant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EtudiantReponseReco>
     */
    public function getEtudiantReponseRecos(): Collection
    {
        return $this->etudiantReponseRecos;
    }

    public function addEtudiantReponseReco(EtudiantReponseReco $etudiantReponseReco): static
    {
        if (!$this->etudiantReponseRecos->contains($etudiantReponseReco)) {
            $this->etudiantReponseRecos->add($etudiantReponseReco);
            $etudiantReponseReco->setEtudiant($this);
        }

        return $this;
    }

    public function removeEtudiantReponseReco(EtudiantReponseReco $etudiantReponseReco): static
    {
        if ($this->etudiantReponseRecos->removeElement($etudiantReponseReco)) {
            // set the owning side to null (unless already changed)
            if ($etudiantReponseReco->getEtudiant() === $this) {
                $etudiantReponseReco->setEtudiant(null);
            }
        }

        return $this;
    }
}
