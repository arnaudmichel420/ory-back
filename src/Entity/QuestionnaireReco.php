<?php

namespace App\Entity;

use App\Entity\Trait\TimestampableTrait;
use App\Repository\QuestionnaireRecoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionnaireRecoRepository::class)]
class QuestionnaireReco
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $actif = null;

    /**
     * @var Collection<int, QuestionReco>
     */
    #[ORM\OneToMany(targetEntity: QuestionReco::class, mappedBy: 'questionnaire', orphanRemoval: true)]
    private Collection $questionRecos;

    public function __construct()
    {
        $this->questionRecos = new ArrayCollection();
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

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * @return Collection<int, QuestionReco>
     */
    public function getQuestionRecos(): Collection
    {
        return $this->questionRecos;
    }

    public function addQuestionReco(QuestionReco $questionReco): static
    {
        if (!$this->questionRecos->contains($questionReco)) {
            $this->questionRecos->add($questionReco);
            $questionReco->setQuestionnaire($this);
        }

        return $this;
    }

    public function removeQuestionReco(QuestionReco $questionReco): static
    {
        if ($this->questionRecos->removeElement($questionReco)) {
            // set the owning side to null (unless already changed)
            if ($questionReco->getQuestionnaire() === $this) {
                $questionReco->setQuestionnaire(null);
            }
        }

        return $this;
    }
}
