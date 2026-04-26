<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MetierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ORM\Entity(repositoryClass: MetierRepository::class)]
class Metier
{
    private const CONTEXTES_TRAVAIL_TYPES = [
        'Conditions de travail et risques professionnels',
        'Horaires et durée du travail',
        'Lieux et déplacements',
        'Publics spécifiques',
        'Statut d\'emploi',
        'Types de structures',
    ];

    #[Groups(['metier:list', 'metier:view'])]
    #[SerializedName('saved')]
    private bool $saved = false;

    #[ORM\Id]
    #[ORM\Column(length: 255)]
    #[Groups(['metier:list', 'metier:view'])]
    private ?string $codeOgr = null;

    /**
     * @var Collection<int, Appellation>
     */
    #[ORM\OneToMany(targetEntity: Appellation::class, mappedBy: 'codeOgrMetier')]
    private Collection $appellations;

    /**
     * @var Collection<int, Etudiant>
     */
    #[ORM\ManyToMany(targetEntity: Etudiant::class, inversedBy: 'favoris')]
    #[ORM\JoinTable(name: 'metier_etudiant')]
    #[ORM\JoinColumn(name: 'metier_code', referencedColumnName: 'code_ogr')]
    #[ORM\InverseJoinColumn(name: 'etudiant_id', referencedColumnName: 'id')]
    private Collection $etudiants;

    #[ORM\Column(length: 255)]
    #[Groups(['metier:list', 'metier:view'])]
    private ?string $codeRome = null;

    #[ORM\Column(length: 255)]
    #[Groups(['metier:list', 'metier:view'])]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['metier:view'])]
    private ?string $definition = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['metier:view'])]
    private ?string $accesMetier = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['metier:list', 'metier:view'])]
    private ?bool $transitionEco = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['metier:list', 'metier:view'])]
    private ?bool $transitionNum = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['metier:view'])]
    private ?bool $emploiReglemente = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['metier:list', 'metier:view'])]
    private ?bool $emploiCadre = null;

    #[ORM\ManyToOne(inversedBy: 'metiers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['metier:view'])]
    private ?SousDomaine $sousDomaine = null;

    /**
     * @var Collection<int, MetierCompetence>
     */
    #[ORM\OneToMany(targetEntity: MetierCompetence::class, mappedBy: 'codeOgrMetier')]
    private Collection $metierCompetences;

    /**
     * @var Collection<int, MetierCentreInteret>
     */
    #[ORM\OneToMany(targetEntity: MetierCentreInteret::class, mappedBy: 'codeOgrMetier')]
    private Collection $metierCentreInterets;

    /**
     * @var Collection<int, MetierSecteur>
     */
    #[ORM\OneToMany(targetEntity: MetierSecteur::class, mappedBy: 'codeOgrMetier')]
    private Collection $metierSecteurs;

    /**
     * @var Collection<int, MetierContexteTravail>
     */
    #[ORM\OneToMany(targetEntity: MetierContexteTravail::class, mappedBy: 'codeOgrMetier')]
    private Collection $metierContexteTravails;

    /**
     * @var Collection<int, Mobilite>
     */
    #[ORM\OneToMany(targetEntity: Mobilite::class, mappedBy: 'codeOgrMetierSource')]
    private Collection $mobilites;

    /**
     * @var Collection<int, EtudiantMetierInteraction>
     */
    #[ORM\OneToMany(targetEntity: EtudiantMetierInteraction::class, mappedBy: 'codeOgrMetier')]
    private Collection $etudiantMetierInteractions;

    /**
     * @var Collection<int, EtudiantMetierScore>
     */
    #[ORM\OneToMany(targetEntity: EtudiantMetierScore::class, mappedBy: 'codeOgrMetier')]
    private Collection $etudiantMetierScores;

    /**
     * @var Collection<int, MetierAttractivite>
     */
    #[ORM\OneToMany(targetEntity: MetierAttractivite::class, mappedBy: 'codeOgrMetier')]
    private Collection $metierAttractivites;

    public function __construct()
    {
        $this->appellations = new ArrayCollection();
        $this->etudiants = new ArrayCollection();
        $this->metierCompetences = new ArrayCollection();
        $this->metierCentreInterets = new ArrayCollection();
        $this->metierSecteurs = new ArrayCollection();
        $this->metierContexteTravails = new ArrayCollection();
        $this->mobilites = new ArrayCollection();
        $this->etudiantMetierInteractions = new ArrayCollection();
        $this->etudiantMetierScores = new ArrayCollection();
        $this->metierAttractivites = new ArrayCollection();
    }

    /**
     * @return Collection<int, Appellation>
     */
    public function getAppellations(): Collection
    {
        return $this->appellations;
    }

    public function addAppellation(Appellation $appellation): static
    {
        if (!$this->appellations->contains($appellation)) {
            $this->appellations->add($appellation);
            $appellation->setCodeOgrMetier($this);
        }

        return $this;
    }

    public function removeAppellation(Appellation $appellation): static
    {
        if ($this->appellations->removeElement($appellation)) {
            // set the owning side to null (unless already changed)
            if ($appellation->getCodeOgrMetier() === $this) {
                $appellation->setCodeOgrMetier(null);
            }
        }

        return $this;
    }

    public function getCodeOgr(): ?string
    {
        return $this->codeOgr;
    }

    public function isSaved(): bool
    {
        return $this->saved;
    }

    public function setSaved(bool $saved): static
    {
        $this->saved = $saved;

        return $this;
    }

    public function setCodeOgr(string $codeOgr): static
    {
        $this->codeOgr = $codeOgr;

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
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): static
    {
        $this->etudiants->removeElement($etudiant);

        return $this;
    }

    public function getCodeRome(): ?string
    {
        return $this->codeRome;
    }

    public function setCodeRome(string $codeRome): static
    {
        $this->codeRome = $codeRome;

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

    public function getDefinition(): ?string
    {
        return $this->definition;
    }

    #[Groups(['metier:list'])]
    #[SerializedName('description')]
    public function getDescriptionForList(): ?string
    {
        return $this->definition;
    }

    public function setDefinition(?string $definition): static
    {
        $this->definition = $definition;

        return $this;
    }

    public function getAccesMetier(): ?string
    {
        return $this->accesMetier;
    }

    public function setAccesMetier(?string $accesMetier): static
    {
        $this->accesMetier = $accesMetier;

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

    public function isEmploiReglemente(): ?bool
    {
        return $this->emploiReglemente;
    }

    public function setEmploiReglemente(?bool $emploiReglemente): static
    {
        $this->emploiReglemente = $emploiReglemente;

        return $this;
    }

    public function isEmploiCadre(): ?bool
    {
        return $this->emploiCadre;
    }

    public function setEmploiCadre(?bool $emploiCadre): static
    {
        $this->emploiCadre = $emploiCadre;

        return $this;
    }

    public function getSousDomaine(): ?SousDomaine
    {
        return $this->sousDomaine;
    }

    public function setSousDomaine(?SousDomaine $sousDomaine): static
    {
        $this->sousDomaine = $sousDomaine;

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
            $metierCompetence->setCodeOgrMetier($this);
        }

        return $this;
    }

    public function removeMetierCompetence(MetierCompetence $metierCompetence): static
    {
        if ($this->metierCompetences->removeElement($metierCompetence)) {
            // set the owning side to null (unless already changed)
            if ($metierCompetence->getCodeOgrMetier() === $this) {
                $metierCompetence->setCodeOgrMetier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MetierCentreInteret>
     */
    public function getMetierCentreInterets(): Collection
    {
        return $this->metierCentreInterets;
    }

    public function addMetierCentreInteret(MetierCentreInteret $metierCentreInteret): static
    {
        if (!$this->metierCentreInterets->contains($metierCentreInteret)) {
            $this->metierCentreInterets->add($metierCentreInteret);
            $metierCentreInteret->setCodeOgrMetier($this);
        }

        return $this;
    }

    public function removeMetierCentreInteret(MetierCentreInteret $metierCentreInteret): static
    {
        if ($this->metierCentreInterets->removeElement($metierCentreInteret)) {
            // set the owning side to null (unless already changed)
            if ($metierCentreInteret->getCodeOgrMetier() === $this) {
                $metierCentreInteret->setCodeOgrMetier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MetierSecteur>
     */
    public function getMetierSecteurs(): Collection
    {
        return $this->metierSecteurs;
    }

    /**
     * @return list<MetierSecteur>
     */
    #[Groups(['metier:list', 'metier:view'])]
    #[SerializedName('secteurs')]
    public function getSecteursForView(): array
    {
        return $this->metierSecteurs->toArray();
    }

    public function addMetierSecteur(MetierSecteur $metierSecteur): static
    {
        if (!$this->metierSecteurs->contains($metierSecteur)) {
            $this->metierSecteurs->add($metierSecteur);
            $metierSecteur->setCodeOgrMetier($this);
        }

        return $this;
    }

    public function removeMetierSecteur(MetierSecteur $metierSecteur): static
    {
        if ($this->metierSecteurs->removeElement($metierSecteur)) {
            // set the owning side to null (unless already changed)
            if ($metierSecteur->getCodeOgrMetier() === $this) {
                $metierSecteur->setCodeOgrMetier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MetierContexteTravail>
     */
    public function getMetierContexteTravails(): Collection
    {
        return $this->metierContexteTravails;
    }

    /**
     * @return array<string, list<array{
     *     id:int|null,
     *     libelleGroupe:?string,
     *     contexteTravail:array{
     *         codeOgr:?string,
     *         libelle:?string,
     *         typeContexte:?string
     *     }
     * }>>
     */
    #[Groups(['metier:view'])]
    #[SerializedName('contextesTravail')]
    public function getContextesTravailForView(): array
    {
        /** @var list<MetierContexteTravail> $contextesTravail */
        $contextesTravail = $this->metierContexteTravails->toArray();
        $typeOrder = array_flip(self::CONTEXTES_TRAVAIL_TYPES);

        usort(
            $contextesTravail,
            static function (MetierContexteTravail $left, MetierContexteTravail $right) use ($typeOrder): int {
                $leftType = $left->getCodeOgrContexte()?->getTypeContexte() ?? 'unknown';
                $rightType = $right->getCodeOgrContexte()?->getTypeContexte() ?? 'unknown';
                $typeComparison = ($typeOrder[$leftType] ?? \PHP_INT_MAX) <=> ($typeOrder[$rightType] ?? \PHP_INT_MAX);

                if (0 !== $typeComparison) {
                    return $typeComparison;
                }

                $idComparison = ($left->getId() ?? 0) <=> ($right->getId() ?? 0);

                if (0 !== $idComparison) {
                    return $idComparison;
                }

                return ($left->getCodeOgrContexte()?->getCodeOgr() ?? '') <=> ($right->getCodeOgrContexte()?->getCodeOgr() ?? '');
            },
        );

        $groupedContextes = [];
        $countsByType = [];

        foreach ($contextesTravail as $contexteTravail) {
            $contexte = $contexteTravail->getCodeOgrContexte();
            $type = $contexte?->getTypeContexte() ?? 'unknown';
            $countsByType[$type] ??= 0;

            if ($countsByType[$type] >= 5) {
                continue;
            }

            $groupedContextes[$type][] = [
                'id' => $contexteTravail->getId(),
                'libelleGroupe' => $contexteTravail->getLibelleGroupe(),
                'contexteTravail' => [
                    'codeOgr' => $contexte?->getCodeOgr(),
                    'libelle' => $contexte?->getLibelle(),
                    'typeContexte' => $contexte?->getTypeContexte(),
                ],
            ];
            ++$countsByType[$type];
        }

        return $groupedContextes;
    }

    public function addMetierContexteTravail(MetierContexteTravail $metierContexteTravail): static
    {
        if (!$this->metierContexteTravails->contains($metierContexteTravail)) {
            $this->metierContexteTravails->add($metierContexteTravail);
            $metierContexteTravail->setCodeOgrMetier($this);
        }

        return $this;
    }

    public function removeMetierContexteTravail(MetierContexteTravail $metierContexteTravail): static
    {
        if ($this->metierContexteTravails->removeElement($metierContexteTravail)) {
            // set the owning side to null (unless already changed)
            if ($metierContexteTravail->getCodeOgrMetier() === $this) {
                $metierContexteTravail->setCodeOgrMetier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mobilite>
     */
    public function getMobilites(): Collection
    {
        return $this->mobilites;
    }

    public function addMobilite(Mobilite $mobilite): static
    {
        if (!$this->mobilites->contains($mobilite)) {
            $this->mobilites->add($mobilite);
            $mobilite->setCodeOgrMetierSource($this);
        }

        return $this;
    }

    public function removeMobilite(Mobilite $mobilite): static
    {
        if ($this->mobilites->removeElement($mobilite)) {
            // set the owning side to null (unless already changed)
            if ($mobilite->getCodeOgrMetierSource() === $this) {
                $mobilite->setCodeOgrMetierSource(null);
            }
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
            $etudiantMetierInteraction->setCodeOgrMetier($this);
        }

        return $this;
    }

    public function removeEtudiantMetierInteraction(EtudiantMetierInteraction $etudiantMetierInteraction): static
    {
        if ($this->etudiantMetierInteractions->removeElement($etudiantMetierInteraction)) {
            // set the owning side to null (unless already changed)
            if ($etudiantMetierInteraction->getCodeOgrMetier() === $this) {
                $etudiantMetierInteraction->setCodeOgrMetier(null);
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
            $etudiantMetierScore->setCodeOgrMetier($this);
        }

        return $this;
    }

    public function removeEtudiantMetierScore(EtudiantMetierScore $etudiantMetierScore): static
    {
        if ($this->etudiantMetierScores->removeElement($etudiantMetierScore)) {
            // set the owning side to null (unless already changed)
            if ($etudiantMetierScore->getCodeOgrMetier() === $this) {
                $etudiantMetierScore->setCodeOgrMetier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MetierAttractivite>
     */
    public function getMetierAttractivites(): Collection
    {
        return $this->metierAttractivites;
    }

    /**
     * @return array<string, list<array{
     *     id:int|null,
     *     libelleEnjeu:?string,
     *     coeurMetier:?int,
     *     competence:array{
     *         codeOgr:?string,
     *         libelle:?string,
     *         type:?string,
     *         transitionEco:?bool,
     *         transitionNum:?bool
     *     }
     * }>>
     */
    #[Groups(['metier:view'])]
    #[SerializedName('competences')]
    public function getCompetencesForView(): array
    {
        /** @var list<MetierCompetence> $competences */
        $competences = $this->metierCompetences->toArray();

        usort(
            $competences,
            static function (MetierCompetence $left, MetierCompetence $right): int {
                $weightComparison = ($right->getCoeurMetier() ?? 0) <=> ($left->getCoeurMetier() ?? 0);

                if (0 !== $weightComparison) {
                    return $weightComparison;
                }

                return ($left->getId() ?? 0) <=> ($right->getId() ?? 0);
            },
        );

        $groupedCompetences = [];
        $countsByType = [];

        foreach ($competences as $competence) {
            $type = $competence->getType()->value;
            $countsByType[$type] ??= 0;

            if ($countsByType[$type] >= 5) {
                continue;
            }

            $groupedCompetences[$type][] = [
                'id' => $competence->getId(),
                'libelleEnjeu' => $competence->getLibelleEnjeu(),
                'coeurMetier' => $competence->getCoeurMetier(),
                'competence' => [
                    'codeOgr' => $competence->getCodeOgrComp()?->getCodeOgr(),
                    'libelle' => $competence->getCodeOgrComp()?->getLibelle(),
                    'type' => $competence->getCodeOgrComp()?->getType()?->value,
                    'transitionEco' => $competence->getCodeOgrComp()?->isTransitionEco(),
                    'transitionNum' => $competence->getCodeOgrComp()?->isTransitionNum(),
                ],
            ];
            ++$countsByType[$type];
        }

        return $groupedCompetences;
    }

    public function addMetierAttractivite(MetierAttractivite $metierAttractivite): static
    {
        if (!$this->metierAttractivites->contains($metierAttractivite)) {
            $this->metierAttractivites->add($metierAttractivite);
            $metierAttractivite->setCodeOgrMetier($this);
        }

        return $this;
    }

    public function removeMetierAttractivite(MetierAttractivite $metierAttractivite): static
    {
        if ($this->metierAttractivites->removeElement($metierAttractivite)) {
            // set the owning side to null (unless already changed)
            if ($metierAttractivite->getCodeOgrMetier() === $this) {
                $metierAttractivite->setCodeOgrMetier(null);
            }
        }

        return $this;
    }
}
