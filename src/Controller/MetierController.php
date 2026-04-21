<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Metier\MetierListQueryDto;
use App\Entity\Metier;
use App\Repository\MetierRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/metiers', name: 'api_metier_')]
final class MetierController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        #[MapQueryString(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] ?MetierListQueryDto $query,
        MetierRepository $metierRepository,
    ): JsonResponse {
        $query ??= new MetierListQueryDto();

        $result = $metierRepository->paginateMetier($query);
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
}
