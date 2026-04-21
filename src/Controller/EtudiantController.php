<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\EtudiantDto;
use App\Dto\EtudiantPasswordDto;
use App\Dto\EtudiantPatchDto;
use App\Entity\Etudiant;
use App\Entity\Utilisateur;
use App\Security\Voter\EtudiantVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/me', name: 'api_me_')]
final class EtudiantController extends AbstractController
{
    #[Route('/etudiant', name: 'etudiant_show', methods: ['GET'])]
    #[IsGranted(EtudiantVoter::VIEW, subject: 'utilisateur')]
    public function show(#[CurrentUser] Utilisateur $utilisateur): JsonResponse
    {
        $etudiant = $utilisateur->getEtudiant();
        if (!$etudiant instanceof Etudiant) {
            return $this->json([
                'message' => 'Profil etudiant introuvable.',
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json($etudiant, context: [
            'groups' => ['user:read'],
        ]);
    }

    #[Route('/etudiant', name: 'etudiant_upsert', methods: ['PUT'])]
    #[IsGranted(EtudiantVoter::EDIT, subject: 'utilisateur')]
    public function upsert(
        #[CurrentUser] Utilisateur $utilisateur,
        #[MapRequestPayload] EtudiantDto $dto,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $etudiant = $utilisateur->getEtudiant();
        $statusCode = Response::HTTP_OK;

        if (!$etudiant instanceof Etudiant) {
            $etudiant = new Etudiant();
            $utilisateur->setEtudiant($etudiant);
            $entityManager->persist($etudiant);
            $statusCode = Response::HTTP_CREATED;
        }

        $etudiant
            ->setNom((string) $dto->nom)
            ->setPrenom((string) $dto->prenom)
            ->setAdresse((string) $dto->adresse)
            ->setVille((string) $dto->ville)
            ->setCodePostal((string) $dto->codePostal)
            ->setTelephone((string) $dto->telephone);

        $entityManager->flush();

        return $this->json($etudiant, $statusCode, context: [
            'groups' => ['user:read'],
        ]);
    }

    #[Route('/etudiant', name: 'etudiant_patch', methods: ['PATCH'])]
    #[IsGranted(EtudiantVoter::EDIT, subject: 'utilisateur')]
    public function patch(
        #[CurrentUser] Utilisateur $utilisateur,
        #[MapRequestPayload] EtudiantPatchDto $dto,
        EntityManagerInterface $entityManager,
        PropertyAccessorInterface $propertyAccessor,
    ): JsonResponse {
        $etudiant = $utilisateur->getEtudiant();
        if (!$etudiant instanceof Etudiant) {
            return $this->json([
                'message' => 'Profil etudiant introuvable.',
            ], Response::HTTP_NOT_FOUND);
        }

        foreach (get_object_vars($dto) as $property => $value) {
            if (null !== $value) {
                $propertyAccessor->setValue($etudiant, $property, $value);
            }
        }

        $entityManager->flush();

        return $this->json($etudiant, context: [
            'groups' => ['user:read'],
        ]);
    }

    #[Route('/etudiant', name: 'etudiant_delete', methods: ['DELETE'])]
    #[IsGranted(EtudiantVoter::DELETE, subject: 'utilisateur')]
    public function delete(
        #[CurrentUser] Utilisateur $utilisateur,
        EntityManagerInterface $entityManager,
    ): Response {
        $etudiant = $utilisateur->getEtudiant();
        if (!$etudiant instanceof Etudiant) {
            return $this->json([
                'message' => 'Profil etudiant introuvable.',
            ], Response::HTTP_NOT_FOUND);
        }

        $utilisateur->setEtudiant(null);
        $entityManager->remove($etudiant);
        $entityManager->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);
    }

    #[Route('/password', name: 'password_update', methods: ['PATCH'])]
    public function updatePassword(
        #[CurrentUser] Utilisateur $utilisateur,
        #[MapRequestPayload] EtudiantPasswordDto $dto,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        if (!$passwordHasher->isPasswordValid($utilisateur, (string) $dto->currentPassword)) {
            return $this->json([
                'message' => 'Le mot de passe actuel est invalide.',
            ], Response::HTTP_FORBIDDEN);
        }

        $utilisateur->setPassword($passwordHasher->hashPassword($utilisateur, (string) $dto->newPassword));
        $entityManager->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
