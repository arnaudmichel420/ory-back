<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Etudiant;
use App\Service\Recommendation\RecommendationService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dummy', name: 'dummy_')]
final class DummyController extends AbstractController
{
    #[Route('/recommendation/{id}', name: 'recommendation', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function requestRecommendationForEtudiant(
        #[MapEntity(id: 'id')] Etudiant $etudiant,
        RecommendationService $recommendationService,
    ): JsonResponse {
        $recommendationService->requestRecommendationForEtudiant($etudiant);

        return $this->json([
            'message' => 'Recommendation demandee.',
            'etudiantId' => $etudiant->getId(),
        ]);
    }
}
