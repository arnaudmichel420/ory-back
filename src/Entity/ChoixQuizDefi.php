<?php

namespace App\Entity;

use App\Repository\ChoixQuizDefiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChoixQuizDefiRepository::class)]
class ChoixQuizDefi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'choixQuizDefis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?QuestionQuizDefi $questionQuiz = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column]
    private ?bool $estCorrect = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestionQuizz(): ?QuestionQuizDefi
    {
        return $this->questionQuiz;
    }

    public function setQuestionQuizz(?QuestionQuizDefi $questionQuiz): static
    {
        $this->questionQuiz = $questionQuiz;

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

    public function isEstCorrect(): ?bool
    {
        return $this->estCorrect;
    }

    public function setEstCorrect(bool $estCorrect): static
    {
        $this->estCorrect = $estCorrect;

        return $this;
    }
}
