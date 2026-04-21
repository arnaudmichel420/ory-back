<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\SecteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/secteurs', name: 'api_secteur_')]
final class SecteurController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(SecteurRepository $secteurRepository): JsonResponse
    {
        return $this->json($secteurRepository->findAll(), context: [
            'groups' => ['secteur:list'],
        ]);
    }
}
