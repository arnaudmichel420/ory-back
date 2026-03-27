<?php

namespace App\Entity;

use App\Entity\Trait\SoftDeleteableTrait;
use App\Repository\QuizDefiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: QuizDefiRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'supprimeLe', timeAware: false, hardDelete: false)]
class QuizDefi
{
    use SoftDeleteableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'quizDefis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Defi $defi = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, QuestionQuizDefi>
     */
    #[ORM\OneToMany(targetEntity: QuestionQuizDefi::class, mappedBy: 'quiz', orphanRemoval: true)]
    private Collection $questionQuizDefis;

    public function __construct()
    {
        $this->questionQuizDefis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDefi(): ?Defi
    {
        return $this->defi;
    }

    public function setDefi(?Defi $defi): static
    {
        $this->defi = $defi;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, QuestionQuizDefi>
     */
    public function getQuestionQuizDefis(): Collection
    {
        return $this->questionQuizDefis;
    }

    public function addQuestionQuizDefi(QuestionQuizDefi $questionQuizDefi): static
    {
        if (!$this->questionQuizDefis->contains($questionQuizDefi)) {
            $this->questionQuizDefis->add($questionQuizDefi);
            $questionQuizDefi->setQuiz($this);
        }

        return $this;
    }

    public function removeQuestionQuizDefi(QuestionQuizDefi $questionQuizDefi): static
    {
        if ($this->questionQuizDefis->removeElement($questionQuizDefi)) {
            // set the owning side to null (unless already changed)
            if ($questionQuizDefi->getQuiz() === $this) {
                $questionQuizDefi->setQuiz(null);
            }
        }

        return $this;
    }
}
