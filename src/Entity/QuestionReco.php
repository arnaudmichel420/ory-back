<?php

namespace App\Entity;

use App\Enum\QuestionRecoTypeEnum;
use App\Repository\QuestionRecoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRecoRepository::class)]
class QuestionReco
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'questionRecos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?QuestionnaireReco $questionnaire = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(enumType: QuestionRecoTypeEnum::class)]
    private ?QuestionRecoTypeEnum $type = null;

    #[ORM\Column]
    private ?int $ordre = null;

    /**
     * @var Collection<int, ChoixReco>
     */
    #[ORM\OneToMany(targetEntity: ChoixReco::class, mappedBy: 'question')]
    private Collection $choixRecos;

    public function __construct()
    {
        $this->choixRecos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestionnaire(): ?QuestionnaireReco
    {
        return $this->questionnaire;
    }

    public function setQuestionnaire(?QuestionnaireReco $questionnaire): static
    {
        $this->questionnaire = $questionnaire;

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

    public function getType(): ?QuestionRecoTypeEnum
    {
        return $this->type;
    }

    public function setType(QuestionRecoTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): static
    {
        $this->ordre = $ordre;

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
            $choixReco->setQuestion($this);
        }

        return $this;
    }

    public function removeChoixReco(ChoixReco $choixReco): static
    {
        if ($this->choixRecos->removeElement($choixReco)) {
            // set the owning side to null (unless already changed)
            if ($choixReco->getQuestion() === $this) {
                $choixReco->setQuestion(null);
            }
        }

        return $this;
    }
}
