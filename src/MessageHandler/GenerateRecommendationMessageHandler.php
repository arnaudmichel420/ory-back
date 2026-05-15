<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Etudiant;
use App\Message\GenerateRecommendationMessage;
use App\Repository\EtudiantRepository;
use App\Service\Recommendation\RecommendationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GenerateRecommendationMessageHandler
{
    public function __construct(
        private EtudiantRepository $etudiantRepository,
        private RecommendationService $recommendationService,
    ) {
    }

    public function __invoke(GenerateRecommendationMessage $message): void
    {
        $etudiant = $this->etudiantRepository->find($message->getEtudiantId());

        if (!$etudiant instanceof Etudiant) {
            return;
        }

        $this->recommendationService->getRecommendationForEtudiant($etudiant);
    }
}
