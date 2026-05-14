<?php

namespace App\Service\Recommendation;


use App\Entity\Etudiant;
use App\Entity\Metier;
use App\Repository\CentreInteretRepository;
use App\Repository\ContexteTravailRepository;
use App\Repository\MetierRepository;
use App\Repository\SecteurRepository;

final class OnboardingCalculatorService
{
    public function __construct(private MetierRepository $metierRepository, private SecteurRepository $secteurRepository, private CentreInteretRepository $centreInteretRepository, private ContexteTravailRepository $contexteTravailRepository) {}

    public function getOnboardingScoreForStudent(Etudiant $etudiant, array $metiers): array
    {
        foreach ($metiers as &$metier) {
            $metier['scoreOnboarding'] = $this->calculateForMetier($etudiant, $metier['codeOgrMetier']);
        }
        dd($metiers);
        return $metiers;
    }

    public function calculateForMetier(Etudiant $etudiant, string $codeOgrMetier): float
    {
        $metier = $this->metierRepository->find($codeOgrMetier);

        if (empty($metier)) {
            return 0.0;
        }

        $secteurScore = $this->scoreSecteurs($etudiant, $metier);
        $centreInteretScore = $this->scoreCentresInteret($etudiant, $metier);
        $contexteScore = $this->scoreContextesTravail($etudiant, $metier);

        return ($secteurScore * 0.40)
            + ($centreInteretScore * 0.35)
            + ($contexteScore * 0.25);
    }

    private function scoreSecteurs(Etudiant $etudiant, Metier $metier): float
    {
        $wantedSecteurs = $this->secteurRepository->getSecteursFromOnboarding($etudiant);
        $metierSecteurs = $this->secteurRepository->getSecteursFromMetier($metier);

        if ($wantedSecteurs === []) {
            return 0.5; // neutre si l'étudiant n'a pas répondu
        }

        $matches = array_intersect($wantedSecteurs, $metierSecteurs);

        return count($matches) / count($wantedSecteurs);
    }

    private function scoreCentresInteret(Etudiant $etudiant, Metier $metier): float
    {
        $wantedCentresInterets = $this->centreInteretRepository->getCentreInteretFromOnboarding($etudiant);
        $metierCentresInterets = $this->centreInteretRepository->getCentreInteretFromMetier($metier);

        if ($wantedCentresInterets === []) {
            return 0.5; // neutre si l'étudiant n'a pas répondu
        }

        $matches = array_intersect($wantedCentresInterets, $metierCentresInterets);

        return count($matches) / count($wantedCentresInterets);
    }

    private function scoreContextesTravail(Etudiant $etudiant, Metier $metier): float
    {
        $wantedContextesTravails = $this->contexteTravailRepository->getContextesTravailFromOnboarding($etudiant);
        $metierContextesTravails = $this->contexteTravailRepository->getContextesTravailFromMetier($metier);

        if ($wantedContextesTravails === []) {
            return 0.5; // neutre si l'étudiant n'a pas répondu
        }

        $matches = array_intersect($wantedContextesTravails, $metierContextesTravails);

        return count($matches) / count($wantedContextesTravails);
    }
}
