<?php

namespace App\Entity;

use App\Entity\Trait\SoftDeleteableTrait;
use App\Enum\QuestionQuizDefiTypeEnum;
use App\Repository\QuestionQuizDefiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: QuestionQuizDefiRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'supprimeLe', timeAware: false, hardDelete: false)]
class QuestionQuizDefi
{
    use SoftDeleteableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'questionQuizDefis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?QuizDefi $quiz = null;

    #[ORM\Column(length: 255)]
    private ?string $question = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $explication = null;

    #[ORM\Column(enumType: QuestionQuizDefiTypeEnum::class)]
    private ?QuestionQuizDefiTypeEnum $type = null;

    #[ORM\Column]
    private ?int $ordre = null;

    /**
     * @var Collection<int, ChoixQuizDefi>
     */
    #[ORM\OneToMany(targetEntity: ChoixQuizDefi::class, mappedBy: 'questionQuiz', orphanRemoval: true)]
    private Collection $choixQuizDefis;

    public function __construct()
    {
        $this->choixQuizDefis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuiz(): ?QuizDefi
    {
        return $this->quiz;
    }

    public function setQuiz(?QuizDefi $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getExplication(): ?string
    {
        return $this->explication;
    }

    public function setExplication(?string $explication): static
    {
        $this->explication = $explication;

        return $this;
    }

    public function getType(): ?QuestionQuizDefiTypeEnum
    {
        return $this->type;
    }

    public function setType(QuestionQuizDefiTypeEnum $type): static
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
     * @return Collection<int, ChoixQuizDefi>
     */
    public function getChoixQuizDefis(): Collection
    {
        return $this->choixQuizDefis;
    }

    public function addChoixQuizDefi(ChoixQuizDefi $choixQuizDefi): static
    {
        if (!$this->choixQuizDefis->contains($choixQuizDefi)) {
            $this->choixQuizDefis->add($choixQuizDefi);
            $choixQuizDefi->setQuestionQuizz($this);
        }

        return $this;
    }

    public function removeChoixQuizDefi(ChoixQuizDefi $choixQuizDefi): static
    {
        if ($this->choixQuizDefis->removeElement($choixQuizDefi)) {
            // set the owning side to null (unless already changed)
            if ($choixQuizDefi->getQuestionQuizz() === $this) {
                $choixQuizDefi->setQuestionQuizz(null);
            }
        }

        return $this;
    }
}
