<?php

declare(strict_types=1);

namespace App\Service\Recommendation;

use App\Entity\Etudiant;
use App\Repository\EtudiantMetierInteractionRepository;
use App\Repository\MetierRepository;

final class InterractionCalculatorService
{
    public function __construct(private MetierRepository $metierRepository, private EtudiantMetierInteractionRepository $etudiantMetierInteractionRepository, private OnboardingCalculatorService $onboardingCalculatorService)
    {
    }

    /**
     * @param list<array{codeOgrMetier: string, scoreAttractivite: float, scoreOnboarding: float}> $onboardingScores
     * @param array<string, list<int>>                                                            $secteursByMetier
     * @param array<string, list<int>>                                                            $centresInteretByMetier
     * @param array<string, list<string>>                                                         $contextesTravailByMetier
     * @param array<string, list<string>>                                                         $competenceByMetier
     *
     * @return list<array{codeOgrMetier: string, scoreAttractivite: float, scoreOnboarding: float, scoreInterraction: float}>
     */
    public function getInterractionScoreForStudent(
        Etudiant $etudiant,
        array $onboardingScores,
        array $secteursByMetier,
        array $centresInteretByMetier,
        array $contextesTravailByMetier,
        array $competenceByMetier,
    ): array {
        $likedMetiersWithScore = $this->etudiantMetierInteractionRepository->getInterractionScoreByMetierForStudent($etudiant);

        if ([] === $likedMetiersWithScore) {
            $result = [];
            foreach ($onboardingScores as $onboardingScore) {
                $result[] = $onboardingScore + ['scoreInterraction' => 0.0];
            }

            return $result;
        }

        $metiers = array_map(static fn ($metier) => $metier['codeOgrMetier'], $likedMetiersWithScore);

        $wantedSecteurs = $this->metierRepository->getSecteursForMetiers($metiers);
        $wantedCentresInteret = $this->metierRepository->getCentresInteretForMetiers($metiers);
        $wantedContextesTravail = $this->metierRepository->getContextesTravailForMetiers($metiers);
        $wantedCompetence = $this->metierRepository->getCompetenceForMetiers($metiers);

        $result = [];
        foreach ($onboardingScores as $onboardingScore) {
            $result[] = $onboardingScore + [
                'scoreInterraction' => $this->calculateForMetier(
                    $onboardingScore['codeOgrMetier'],
                    $likedMetiersWithScore,
                    $secteursByMetier,
                    $centresInteretByMetier,
                    $contextesTravailByMetier,
                    $competenceByMetier,
                    $wantedSecteurs,
                    $wantedCentresInteret,
                    $wantedContextesTravail,
                    $wantedCompetence,
                ),
            ];
        }

        return $result;
    }

    /**
     * @param list<array{codeOgrMetier: string, interractionScore: float}> $likedMetiersWithScore
     * @param array<string, list<int>>                                      $secteursByMetier
     * @param array<string, list<int>>                                      $centresInteretByMetier
     * @param array<string, list<string>>                                   $contextesTravailByMetier
     * @param array<string, list<string>>                                   $competenceByMetier
     * @param array<string, list<int>>                                      $wantedSecteurs
     * @param array<string, list<int>>                                      $wantedCentresInteret
     * @param array<string, list<string>>                                   $wantedContextesTravail
     * @param array<string, list<string>>                                   $wantedCompetence
     */
    public function calculateForMetier(
        string $codeOgrMetier,
        array $likedMetiersWithScore,
        array $secteursByMetier,
        array $centresInteretByMetier,
        array $contextesTravailByMetier,
        array $competenceByMetier,
        array $wantedSecteurs,
        array $wantedCentresInteret,
        array $wantedContextesTravail,
        array $wantedCompetence,
    ): float {
        $directScore = $this->scoreDirect($likedMetiersWithScore, $codeOgrMetier);
        $similarityScore = $this->scoreMetierSimilaireAuxInterractions(
            $codeOgrMetier,
            $likedMetiersWithScore,
            $secteursByMetier,
            $centresInteretByMetier,
            $contextesTravailByMetier,
            $competenceByMetier,
            $wantedSecteurs,
            $wantedCentresInteret,
            $wantedContextesTravail,
            $wantedCompetence,
        );

        return ($directScore * 0.60)
            + ($similarityScore * 0.40);
    }

    /**
     * @param list<array{codeOgrMetier: string, interractionScore: float}> $likedMetiersWithScore
     */
    private function scoreDirect(array $likedMetiersWithScore, string $metier): float
    {
        $index = array_search($metier, array_column($likedMetiersWithScore, 'codeOgrMetier'));

        return false !== $index ? $likedMetiersWithScore[$index]['interractionScore'] : 0;
    }

    /**
     * @param list<array{codeOgrMetier: string, interractionScore: float}> $likedMetiersWithScore
     * @param array<string, list<int>>                                      $secteursByMetier
     * @param array<string, list<int>>                                      $centresInteretByMetier
     * @param array<string, list<string>>                                   $contextesTravailByMetier
     * @param array<string, list<string>>                                   $competenceByMetier
     * @param array<string, list<int>>                                      $wantedSecteurs
     * @param array<string, list<int>>                                      $wantedCentresInteret
     * @param array<string, list<string>>                                   $wantedContextesTravail
     * @param array<string, list<string>>                                   $wantedCompetence
     */
    private function scoreMetierSimilaireAuxInterractions(
        string $codeOgrMetier,
        array $likedMetiersWithScore,
        array $secteursByMetier,
        array $centresInteretByMetier,
        array $contextesTravailByMetier,
        array $competenceByMetier,
        array $wantedSecteurs,
        array $wantedCentresInteret,
        array $wantedContextesTravail,
        array $wantedCompetence,
    ): float {
        if ([] === $likedMetiersWithScore) {
            return 0.5;
        }

        $bestScore = 0.0;

        foreach ($likedMetiersWithScore as $likedMetierWithScore) {
            $likedMetier = $likedMetierWithScore['codeOgrMetier'];

            $similarity = $this->computeMetierSimilarity(
                $likedMetier,
                $codeOgrMetier,
                $secteursByMetier,
                $centresInteretByMetier,
                $contextesTravailByMetier,
                $competenceByMetier,
                $wantedSecteurs,
                $wantedCentresInteret,
                $wantedContextesTravail,
                $wantedCompetence,
            );

            $similarity *= $likedMetierWithScore['interractionScore'];

            if ($similarity > $bestScore) {
                $bestScore = $similarity;
            }
        }

        return $bestScore;
    }

    /**
     * @param array<string, list<int>>    $secteursByMetier
     * @param array<string, list<int>>    $centresInteretByMetier
     * @param array<string, list<string>> $contextesTravailByMetier
     * @param array<string, list<string>> $competenceByMetier
     * @param array<string, list<int>>    $wantedSecteurs
     * @param array<string, list<int>>    $wantedCentresInteret
     * @param array<string, list<string>> $wantedContextesTravail
     * @param array<string, list<string>> $wantedCompetence
     */
    private function computeMetierSimilarity(
        string $likedMetier,
        string $codeOgrMetier,
        array $secteursByMetier,
        array $centresInteretByMetier,
        array $contextesTravailByMetier,
        array $competenceByMetier,
        array $wantedSecteurs,
        array $wantedCentresInteret,
        array $wantedContextesTravail,
        array $wantedCompetence,
    ): float {
        $secteurScore = $this->onboardingCalculatorService->computeRatioScore(
            $wantedSecteurs[$likedMetier] ?? [],
            $secteursByMetier[$codeOgrMetier] ?? [],
        );

        $centreInteretScore = $this->onboardingCalculatorService->computeRatioScore(
            $wantedCentresInteret[$likedMetier] ?? [],
            $centresInteretByMetier[$codeOgrMetier] ?? [],
        );

        $contexteScore = $this->onboardingCalculatorService->computeRatioScore(
            $wantedContextesTravail[$likedMetier] ?? [],
            $contextesTravailByMetier[$codeOgrMetier] ?? [],
        );

        $competenceScore = $this->onboardingCalculatorService->computeRatioScore(
            $wantedCompetence[$likedMetier] ?? [],
            $competenceByMetier[$codeOgrMetier] ?? [],
        );

        return ($secteurScore * 0.35)
            + ($centreInteretScore * 0.25)
            + ($competenceScore * 0.25)
            + ($contexteScore * 0.15);
    }
}
