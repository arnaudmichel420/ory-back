<?php

declare(strict_types=1);

namespace App\Service\Recommendation;

use App\Entity\Etudiant;
use App\Repository\EtudiantMetierScoreRepository;
use App\Repository\MetierRepository;

final class RecommendationService
{
    public function __construct(private AttractiviteCalculatorService $attractiviteCalculatorService, private InterractionCalculatorService $interractionCalculatorService, private OnboardingCalculatorService $onboardingCalculatorService, private EtudiantMetierScoreRepository $etudiantMetierScoreRepository, private MetierRepository $metierRepository)
    {
    }

    public function requestRecommendationForEtudiant(Etudiant $etudiant): void
    {
        $recommendationThreshold = new \DateTimeImmutable()->modify('-1 hour');
        $lastRecommendation = $this->etudiantMetierScoreRepository->getLastRecommendationDateForEtudiant($etudiant);

        if (empty($lastRecommendation) || $lastRecommendation < $recommendationThreshold) {
            $this->getRecommendationForEtudiant($etudiant);
        }
    }

    public function getRecommendationForEtudiant(Etudiant $etudiant): void
    {
        $attractiveMetiers = $this->attractiviteCalculatorService->getAttractiveMetier($etudiant);

        $metiers = array_map(static fn (array $metier): string => $metier['codeOgrMetier'], $attractiveMetiers);

        $secteursByMetier = $this->metierRepository->getSecteursForMetiers($metiers);
        $centresInteretByMetier = $this->metierRepository->getCentresInteretForMetiers($metiers);
        $contextesTravailByMetier = $this->metierRepository->getContextesTravailForMetiers($metiers);
        $competenceByMetier = $this->metierRepository->getCompetenceForMetiers($metiers);

        $onboardingScore = $this->onboardingCalculatorService->getOnboardingScoreForStudent(
            $etudiant,
            $attractiveMetiers,
            $secteursByMetier,
            $centresInteretByMetier,
            $contextesTravailByMetier
        );

        $interractionScore = $this->interractionCalculatorService->getInterractionScoreForStudent(
            $etudiant,
            $onboardingScore,
            $secteursByMetier,
            $centresInteretByMetier,
            $contextesTravailByMetier,
            $competenceByMetier
        );

        /** @var array<string, float> $recommendedMetiers */
        $recommendedMetiers = [];
        foreach ($interractionScore as $metierScore) {
            $scoreInteractions = $metierScore['scoreInterraction'];
            $scoreAttractiviteLocale = $metierScore['scoreAttractivite'];
            $scoreOnboarding = $metierScore['scoreOnboarding'];

            $recommendedMetiers[$metierScore['codeOgrMetier']] = $scoreOnboarding * 0.45
                + $scoreInteractions * 0.30
                + $scoreAttractiviteLocale * 0.25;
        }

        arsort($recommendedMetiers);
        dd($recommendedMetiers);
    }
}
