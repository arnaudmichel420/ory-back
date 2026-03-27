<?php

namespace App\Entity;

use App\Entity\Trait\SoftDeleteableTrait;
use App\Repository\ChoixRecoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: ChoixRecoRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'supprimeLe', timeAware: false, hardDelete: false)]
class ChoixReco
{
    use SoftDeleteableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'choixRecos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?QuestionReco $question = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'choixRecos')]
    private ?CentreInteret $centreInteret = null;

    #[ORM\ManyToOne(inversedBy: 'choixRecos')]
    private ?Secteur $secteur = null;

    #[ORM\ManyToOne(inversedBy: 'choixRecos')]
    #[ORM\JoinColumn(referencedColumnName: "code_ogr")]
    private ?ContexteTravail $contexteTravail = null;

    /**
     * @var Collection<int, EtudiantReponseReco>
     */
    #[ORM\OneToMany(targetEntity: EtudiantReponseReco::class, mappedBy: 'choix')]
    private Collection $etudiantReponseRecos;

    public function __construct()
    {
        $this->etudiantReponseRecos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?QuestionReco
    {
        return $this->question;
    }

    public function setQuestion(?QuestionReco $question): static
    {
        $this->question = $question;

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

    public function getCentreInteret(): ?CentreInteret
    {
        return $this->centreInteret;
    }

    public function setCentreInteret(?CentreInteret $centreInteret): static
    {
        $this->centreInteret = $centreInteret;

        return $this;
    }

    public function getSecteur(): ?Secteur
    {
        return $this->secteur;
    }

    public function setSecteur(?Secteur $secteur): static
    {
        $this->secteur = $secteur;

        return $this;
    }

    public function getContexteTravail(): ?ContexteTravail
    {
        return $this->contexteTravail;
    }

    public function setContexteTravail(?ContexteTravail $contexteTravail): static
    {
        $this->contexteTravail = $contexteTravail;

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
            $etudiantReponseReco->setChoix($this);
        }

        return $this;
    }

    public function removeEtudiantReponseReco(EtudiantReponseReco $etudiantReponseReco): static
    {
        if ($this->etudiantReponseRecos->removeElement($etudiantReponseReco)) {
            // set the owning side to null (unless already changed)
            if ($etudiantReponseReco->getChoix() === $this) {
                $etudiantReponseReco->setChoix(null);
            }
        }

        return $this;
    }
}
