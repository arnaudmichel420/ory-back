<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Metier\MetierInteractionToggleDto;
use App\Dto\Metier\MetierListQueryDto;
use App\Entity\Etudiant;
use App\Entity\Metier;
use App\Entity\Utilisateur;
use App\Enum\EtudiantMetierInteractionTypeEnum;
use App\Repository\EtudiantMetierInteractionRepository;
use App\Repository\MetierRepository;
use App\Service\EtudiantMetierInteractionService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/api/metiers', name: 'api_metier_')]
final class MetierController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        #[MapQueryString(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] ?MetierListQueryDto $query,
        #[CurrentUser] ?Utilisateur $utilisateur,
        MetierRepository $metierRepository,
    ): JsonResponse {
        $query ??= new MetierListQueryDto();
        $etudiant = $utilisateur?->getEtudiant();

        $result = $metierRepository->paginateMetier($query, $etudiant);
        $totalPages = 0 === $result['total'] ? 0 : (int) ceil($result['total'] / $query->perPage);

        return $this->json([
            'items' => $result['items'],
            'meta' => [
                'page' => $query->page,
                'perPage' => $query->perPage,
                'total' => $result['total'],
                'totalPages' => $totalPages,
                'sort' => $query->sort,
            ],
        ], context: [
            'groups' => ['metier:list'],
        ]);
    }

    #[Route('/{codeOgr}', name: 'show', methods: ['GET'])]
    public function show(
        #[MapEntity(id: 'codeOgr')] ?Metier $metier,
    ): JsonResponse {
        if (null === $metier) {
            throw $this->createNotFoundException('Metier introuvable.');
        }

        return $this->json($metier, context: [
            'groups' => ['metier:view'],
        ]);
    }

    #[Route('/{codeOgr}/save', name: 'save_toggle', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function toggleSave(
        #[MapEntity(id: 'codeOgr')] ?Metier $metier,
        #[CurrentUser] Utilisateur $utilisateur,
        EtudiantMetierInteractionRepository $interactionRepository,
        EtudiantMetierInteractionService $interactionService,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        if (null === $metier) {
            throw $this->createNotFoundException('Metier introuvable.');
        }

        $etudiant = $utilisateur->getEtudiant();
        if (!$etudiant instanceof Etudiant) {
            return $this->json([
                'message' => 'Profil etudiant introuvable.',
            ], Response::HTTP_NOT_FOUND);
        }

        $savedInteraction = $interactionRepository->findOneByEtudiantMetierAndType(
            $etudiant,
            $metier,
            EtudiantMetierInteractionTypeEnum::SAUVEGARDE,
        );

        $saved = null === $savedInteraction;

        if ($saved) {
            $etudiant->addFavori($metier);
            $interactionService->addInteraction($etudiant, $metier, EtudiantMetierInteractionTypeEnum::SAUVEGARDE);
        } else {
            $etudiant->removeFavori($metier);
            $interactionService->removeInteraction($etudiant, $metier, EtudiantMetierInteractionTypeEnum::SAUVEGARDE);
        }

        $entityManager->flush();

        return $this->json([
            'saved' => $saved,
        ]);
    }

    #[Route('/{codeOgr}/interactions', name: 'interaction_toggle', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function toggleInteraction(
        #[MapEntity(id: 'codeOgr')] ?Metier $metier,
        #[CurrentUser] Utilisateur $utilisateur,
        #[MapRequestPayload(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] MetierInteractionToggleDto $dto,
        EtudiantMetierInteractionService $interactionService,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        if (null === $metier) {
            throw $this->createNotFoundException('Metier introuvable.');
        }

        $etudiant = $utilisateur->getEtudiant();
        if (!$etudiant instanceof Etudiant) {
            return $this->json([
                'message' => 'Profil etudiant introuvable.',
            ], Response::HTTP_NOT_FOUND);
        }

        $type = $dto->getTypeEnum();

        $interactionService->addInteraction($etudiant, $metier, $type);

        $entityManager->flush();

        return $this->json([
            'active' => true,
            'type' => $type->value,
        ]);
    }
}
