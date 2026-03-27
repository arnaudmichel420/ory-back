<?php

namespace App\Entity;

use App\Entity\Trait\SoftDeleteableTrait;
use App\Entity\Trait\TimestampableTrait;
use App\Enum\DefiTypeEnum;
use App\Repository\DefiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: DefiRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'supprimeLe', timeAware: false, hardDelete: false)]
class Defi
{
    use TimestampableTrait;
    use SoftDeleteableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(enumType: DefiTypeEnum::class)]
    private ?DefiTypeEnum $type = null;

    #[ORM\Column]
    private ?bool $estActif = null;

    /**
     * @var Collection<int, ActionDefi>
     */
    #[ORM\OneToMany(targetEntity: ActionDefi::class, mappedBy: 'defi', orphanRemoval: true)]
    private Collection $actionDefis;

    /**
     * @var Collection<int, QuizDefi>
     */
    #[ORM\OneToMany(targetEntity: QuizDefi::class, mappedBy: 'defi', orphanRemoval: true)]
    private Collection $quizDefis;

    /**
     * @var Collection<int, Collectionnable>
     */
    #[ORM\ManyToMany(targetEntity: Collectionnable::class, mappedBy: 'defi')]
    private Collection $collectionnables;

    /**
     * @var Collection<int, EtudiantDefi>
     */
    #[ORM\OneToMany(targetEntity: EtudiantDefi::class, mappedBy: 'defi')]
    private Collection $etudiantDefis;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'defisPrerequis')]
    private ?self $prerequis = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'prerequis')]
    private Collection $defisPrerequis;

    public function __construct()
    {
        $this->actionDefis = new ArrayCollection();
        $this->quizDefis = new ArrayCollection();
        $this->collectionnables = new ArrayCollection();
        $this->etudiantDefis = new ArrayCollection();
        $this->defisPrerequis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?DefiTypeEnum
    {
        return $this->type;
    }

    public function setType(DefiTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isEstActif(): ?bool
    {
        return $this->estActif;
    }

    public function setEstActif(bool $estActif): static
    {
        $this->estActif = $estActif;

        return $this;
    }

    /**
     * @return Collection<int, ActionDefi>
     */
    public function getActionDefis(): Collection
    {
        return $this->actionDefis;
    }

    public function addActionDefi(ActionDefi $actionDefi): static
    {
        if (!$this->actionDefis->contains($actionDefi)) {
            $this->actionDefis->add($actionDefi);
            $actionDefi->setDefi($this);
        }

        return $this;
    }

    public function removeActionDefi(ActionDefi $actionDefi): static
    {
        if ($this->actionDefis->removeElement($actionDefi)) {
            // set the owning side to null (unless already changed)
            if ($actionDefi->getDefi() === $this) {
                $actionDefi->setDefi(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QuizDefi>
     */
    public function getQuizDefis(): Collection
    {
        return $this->quizDefis;
    }

    public function addQuizDefi(QuizDefi $quizDefi): static
    {
        if (!$this->quizDefis->contains($quizDefi)) {
            $this->quizDefis->add($quizDefi);
            $quizDefi->setDefi($this);
        }

        return $this;
    }

    public function removeQuizDefi(QuizDefi $quizDefi): static
    {
        if ($this->quizDefis->removeElement($quizDefi)) {
            // set the owning side to null (unless already changed)
            if ($quizDefi->getDefi() === $this) {
                $quizDefi->setDefi(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Collectionnable>
     */
    public function getCollectionnables(): Collection
    {
        return $this->collectionnables;
    }

    public function addCollectionnable(Collectionnable $collectionnable): static
    {
        if (!$this->collectionnables->contains($collectionnable)) {
            $this->collectionnables->add($collectionnable);
            $collectionnable->addDefi($this);
        }

        return $this;
    }

    public function removeCollectionnable(Collectionnable $collectionnable): static
    {
        if ($this->collectionnables->removeElement($collectionnable)) {
            $collectionnable->removeDefi($this);
        }

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
            $etudiantDefi->setDefi($this);
        }

        return $this;
    }

    public function removeEtudiantDefi(EtudiantDefi $etudiantDefi): static
    {
        if ($this->etudiantDefis->removeElement($etudiantDefi)) {
            // set the owning side to null (unless already changed)
            if ($etudiantDefi->getDefi() === $this) {
                $etudiantDefi->setDefi(null);
            }
        }

        return $this;
    }

    public function getPrerequis(): ?self
    {
        return $this->prerequis;
    }

    public function setPrerequis(?self $prerequis): static
    {
        $this->prerequis = $prerequis;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getDefisPrerequis(): Collection
    {
        return $this->defisPrerequis;
    }

    public function addDefisPrerequi(self $defisPrerequi): static
    {
        if (!$this->defisPrerequis->contains($defisPrerequi)) {
            $this->defisPrerequis->add($defisPrerequi);
            $defisPrerequi->setPrerequis($this);
        }

        return $this;
    }

    public function removeDefisPrerequi(self $defisPrerequi): static
    {
        if ($this->defisPrerequis->removeElement($defisPrerequi)) {
            // set the owning side to null (unless already changed)
            if ($defisPrerequi->getPrerequis() === $this) {
                $defisPrerequi->setPrerequis(null);
            }
        }

        return $this;
    }
}
