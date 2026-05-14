<?php

namespace App\Service\Recommendation;

use App\Entity\Etudiant;
use App\Repository\EtudiantMetierScoreRepository;
use DateTime;
use DateTimeImmutable;

final class RecommendationService
{
    public function __construct(private AttractiviteCalculatorService $attractiviteCalculatorService, private InterractionCalculatorService $interractionCalculatorService, private OnboardingCalculatorService $onboardingCalculatorService, private EtudiantMetierScoreRepository $etudiantMetierScoreRepository) {}

    public function requestRecommendationForEtudiant(Etudiant $etudiant): void
    {
        $recommendationThreshold = new DateTimeImmutable()->modify('-1 hour');
        $lastRecommendation = $this->etudiantMetierScoreRepository->getLastRecommendationDateForEtudiant($etudiant);

        if ($lastRecommendation < $recommendationThreshold) {
            $this->getRecommendationForEtudiant($etudiant);
        }
    }

    public function getRecommendationForEtudiant(Etudiant $etudiant): void
    {
        $attractiveMetiers = $this->attractiviteCalculatorService->getAttractiveMetier($etudiant);

        $onboardingScore = $this->onboardingCalculatorService->getOnboardingScoreForStudent($etudiant, $attractiveMetiers);
        $interractionScore = $this->interractionCalculatorService->getInterractionScoreForStudent($etudiant, $attractiveMetiers);

    }
}
