<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Reco\RecoOnboardingReponsesDto;
use App\Entity\Etudiant;
use App\Entity\QuestionnaireReco;
use App\Entity\Utilisateur;
use App\Message\GenerateRecommendationMessage;
use App\Service\RecoOnboardingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/questionnaires-reco/onboarding', name: 'api_questionnaire_reco_onboarding_')]
final class RecoOnboardingController extends AbstractController
{
    #[Route('', name: 'show', methods: ['GET'])]
    public function show(RecoOnboardingService $recoOnboardingService): JsonResponse
    {
        $questionnaire = $recoOnboardingService->findQuestionnaireActif();

        if (!$questionnaire instanceof QuestionnaireReco) {
            throw $this->createNotFoundException('Questionnaire onboarding recommandation introuvable.');
        }

        return $this->json($recoOnboardingService->normaliserQuestionnaire($questionnaire));
    }

    #[Route('/reponses', name: 'reponses_replace', methods: ['PUT'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function replaceReponses(
        #[CurrentUser] Utilisateur $utilisateur,
        #[MapRequestPayload(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] RecoOnboardingReponsesDto $dto,
        RecoOnboardingService $recoOnboardingService,
        MessageBusInterface $messageBus,
    ): Response {
        $etudiant = $utilisateur->getEtudiant();
        if (!$etudiant instanceof Etudiant) {
            return $this->json([
                'message' => 'Profil etudiant introuvable.',
            ], Response::HTTP_NOT_FOUND);
        }

        $questionnaire = $recoOnboardingService->findQuestionnaireActif();
        if (!$questionnaire instanceof QuestionnaireReco) {
            throw $this->createNotFoundException('Questionnaire onboarding recommandation introuvable.');
        }

        try {
            $recoOnboardingService->remplacerReponses($etudiant, $questionnaire, $dto->getChoixIds());
        } catch (\InvalidArgumentException $exception) {
            return $this->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->dispatchRecommendationMessage($etudiant, $messageBus);

        return new Response(status: Response::HTTP_NO_CONTENT);
    }

    private function dispatchRecommendationMessage(Etudiant $etudiant, MessageBusInterface $messageBus): void
    {
        $etudiantId = $etudiant->getId();

        if (null === $etudiantId) {
            return;
        }

        $messageBus->dispatch(new GenerateRecommendationMessage($etudiantId));
    }
}
