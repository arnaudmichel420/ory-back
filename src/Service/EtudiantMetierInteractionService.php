<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Etudiant;
use App\Entity\EtudiantMetierInteraction;
use App\Entity\Metier;
use App\Enum\EtudiantMetierInteractionTypeEnum;
use App\Repository\EtudiantMetierInteractionRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class EtudiantMetierInteractionService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EtudiantMetierInteractionRepository $interactionRepository,
    ) {
    }

    public function addInteraction(
        Etudiant $etudiant,
        Metier $metier,
        EtudiantMetierInteractionTypeEnum $type,
        ?int $poids = null,
    ): EtudiantMetierInteraction {
        $interaction = $this->interactionRepository->findOneByEtudiantMetierAndType($etudiant, $metier, $type);

        if ($interaction instanceof EtudiantMetierInteraction) {
            return $interaction;
        }

        $interaction = (new EtudiantMetierInteraction())
            ->setEtudiant($etudiant)
            ->setCodeOgrMetier($metier)
            ->setType($type)
            ->setPoids($poids ?? $type->getPoids());

        $this->entityManager->persist($interaction);

        return $interaction;
    }

    public function removeInteraction(
        Etudiant $etudiant,
        Metier $metier,
        EtudiantMetierInteractionTypeEnum $type,
    ): void {
        $interaction = $this->interactionRepository->findOneByEtudiantMetierAndType($etudiant, $metier, $type);

        if ($interaction instanceof EtudiantMetierInteraction) {
            $this->entityManager->remove($interaction);
        }
    }
}
