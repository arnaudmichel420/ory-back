<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Auth\LoginRequestDto;
use App\Dto\Auth\RegisterRequestDto;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/auth', name: 'api_auth_')]
final class AuthController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(
        #[MapRequestPayload] LoginRequestDto $dto,
        UtilisateurRepository $utilisateurRepository,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtTokenManager,
        NormalizerInterface $normalizer,
    ): JsonResponse {
        $email = mb_strtolower(trim((string) $dto->email));
        $password = (string) $dto->password;
        $utilisateur = $utilisateurRepository->findOneBy(['email' => $email]);

        if (!$utilisateur instanceof Utilisateur || !$passwordHasher->isPasswordValid($utilisateur, $password)) {
            return $this->json([
                'message' => 'Identifiants invalides.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'token' => $jwtTokenManager->create($utilisateur),
            'user' => $normalizer->normalize($utilisateur, context: [
                'groups' => ['user:read'],
            ]),
        ]);
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload] RegisterRequestDto $dto,
        UtilisateurRepository $utilisateurRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        NormalizerInterface $normalizer,
    ): JsonResponse {
        $email = mb_strtolower(trim((string) $dto->email));
        $password = (string) $dto->password;

        if ($utilisateurRepository->findOneBy(['email' => $email]) instanceof Utilisateur) {
            return $this->json([
                'message' => 'Un utilisateur avec cet email existe deja.',
            ], Response::HTTP_CONFLICT);
        }

        $utilisateur = (new Utilisateur())
            ->setEmail($email)
            ->setRoles(['ROLE_USER']);

        $utilisateur->setPassword($passwordHasher->hashPassword($utilisateur, $password));

        try {
            $entityManager->persist($utilisateur);
            $entityManager->flush();
        } catch (UniqueConstraintViolationException) {
            return $this->json([
                'message' => 'Un utilisateur avec cet email existe deja.',
            ], Response::HTTP_CONFLICT);
        }

        return $this->json($normalizer->normalize($utilisateur, context: [
            'groups' => ['user:read'],
        ]), Response::HTTP_CREATED);
    }

    #[Route('/me', name: 'me', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function me(): JsonResponse
    {
        $utilisateur = $this->getUser();

        if (!$utilisateur instanceof Utilisateur) {
            return $this->json([
                'message' => 'Utilisateur non authentifie.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json($utilisateur, context: [
            'groups' => ['user:read'],
        ]);
    }
}
