<?php

namespace App\Entity;

use App\Enum\MetierCompetenceTypeEnum;
use App\Repository\CompetenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompetenceRepository::class)]
class Competence
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private ?string $codeOgr = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(enumType: MetierCompetenceTypeEnum::class)]
    private ?MetierCompetenceTypeEnum $type = null;

    #[ORM\Column(nullable: true)]
    private ?bool $transitionEco = null;

    #[ORM\Column(nullable: true)]
    private ?bool $transitionNum = null;

    /**
     * @var Collection<int, MetierCompetence>
     */
    #[ORM\OneToMany(targetEntity: MetierCompetence::class, mappedBy: 'codeOgrComp')]
    private Collection $metierCompetences;

    public function __construct()
    {
        $this->metierCompetences = new ArrayCollection();
    }

    public function getCodeOgr(): ?string
    {
        return $this->codeOgr;
    }

    public function setCodeOgr(string $codeOgr): static
    {
        $this->codeOgr = $codeOgr;

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

    public function getType(): ?MetierCompetenceTypeEnum
    {
        return $this->type;
    }

    public function setType(MetierCompetenceTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isTransitionEco(): ?bool
    {
        return $this->transitionEco;
    }

    public function setTransitionEco(?bool $transitionEco): static
    {
        $this->transitionEco = $transitionEco;

        return $this;
    }

    public function isTransitionNum(): ?bool
    {
        return $this->transitionNum;
    }

    public function setTransitionNum(?bool $transitionNum): static
    {
        $this->transitionNum = $transitionNum;

        return $this;
    }

    /**
     * @return Collection<int, MetierCompetence>
     */
    public function getMetierCompetences(): Collection
    {
        return $this->metierCompetences;
    }

    public function addMetierCompetence(MetierCompetence $metierCompetence): static
    {
        if (!$this->metierCompetences->contains($metierCompetence)) {
            $this->metierCompetences->add($metierCompetence);
            $metierCompetence->setCodeOgrComp($this);
        }

        return $this;
    }

    public function removeMetierCompetence(MetierCompetence $metierCompetence): static
    {
        if ($this->metierCompetences->removeElement($metierCompetence)) {
            // set the owning side to null (unless already changed)
            if ($metierCompetence->getCodeOgrComp() === $this) {
                $metierCompetence->setCodeOgrComp(null);
            }
        }

        return $this;
    }
}
