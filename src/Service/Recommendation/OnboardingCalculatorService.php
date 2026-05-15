<?php

declare(strict_types=1);

namespace App\Service\Recommendation;

use App\Entity\Etudiant;
use App\Repository\CentreInteretRepository;
use App\Repository\ContexteTravailRepository;
use App\Repository\SecteurRepository;

final class OnboardingCalculatorService
{
    public function __construct(private SecteurRepository $secteurRepository, private CentreInteretRepository $centreInteretRepository, private ContexteTravailRepository $contexteTravailRepository)
    {
    }

    /**
     * @param list<array{codeOgrMetier: string, scoreAttractivite: float}> $attractiveMetiers
     * @param array<string, list<int>>                                         $secteursByMetier
     * @param array<string, list<int>>                                         $centresInteretByMetier
     * @param array<string, list<string>>                                      $contextesTravailByMetier
     *
     * @return list<array{codeOgrMetier: string, scoreAttractivite: float, scoreOnboarding: float}>
     */
    public function getOnboardingScoreForStudent(
        Etudiant $etudiant,
        array $attractiveMetiers,
        array $secteursByMetier,
        array $centresInteretByMetier,
        array $contextesTravailByMetier,
    ): array {
        $wantedSecteurs = $this->secteurRepository->getSecteursFromOnboarding($etudiant);
        $wantedCentresInteret = $this->centreInteretRepository->getCentreInteretFromOnboarding($etudiant);
        $wantedContextesTravail = $this->contexteTravailRepository->getContextesTravailFromOnboarding($etudiant);

        $result = [];
        foreach ($attractiveMetiers as $attractiveMetier) {
            $result[] = $attractiveMetier + [
                'scoreOnboarding' => $this->calculateForMetier(
                    $attractiveMetier['codeOgrMetier'],
                    $secteursByMetier,
                    $centresInteretByMetier,
                    $contextesTravailByMetier,
                    $wantedSecteurs,
                    $wantedCentresInteret,
                    $wantedContextesTravail,
                ),
            ];
        }

        return $result;
    }

    /**
     * @param array<string, list<int>>    $secteursByMetier
     * @param array<string, list<int>>    $centresInteretByMetier
     * @param array<string, list<string>> $contextesTravailByMetier
     * @param list<int>                   $wantedSecteurs
     * @param list<int>                   $wantedCentresInteret
     * @param list<string>                $wantedContextesTravail
     */
    public function calculateForMetier(
        string $codeOgr,
        array $secteursByMetier,
        array $centresInteretByMetier,
        array $contextesTravailByMetier,
        array $wantedSecteurs,
        array $wantedCentresInteret,
        array $wantedContextesTravail,
    ): float {
        $secteurScore = $this->computeRatioScore(
            $wantedSecteurs,
            $secteursByMetier[$codeOgr] ?? [],
        );

        $centreInteretScore = $this->computeRatioScore(
            $wantedCentresInteret,
            $centresInteretByMetier[$codeOgr] ?? [],
        );

        $contexteScore = $this->computeRatioScore(
            $wantedContextesTravail,
            $contextesTravailByMetier[$codeOgr] ?? [],
        );

        return round(($secteurScore * 0.40)
            + ($centreInteretScore * 0.35)
            + ($contexteScore * 0.25), 2);
    }

    /**
     * @param list<int|string> $wantedValues
     * @param list<int|string> $metierValues
     */
    public function computeRatioScore(array $wantedValues, array $metierValues): float
    {
        if ([] === $wantedValues) {
            return 0.5;
        }

        if ([] === $metierValues) {
            return 0.0;
        }

        $matches = array_intersect($wantedValues, $metierValues);

        return \count($matches) / \count($wantedValues);
    }
}
