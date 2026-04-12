<?php

declare(strict_types=1);

namespace App\Service\PoleEmploi;

use App\Entity\CentreInteret;
use App\Repository\CentreInteretRepository;
use Doctrine\ORM\EntityManagerInterface;

class CentreInteretImportService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CentreInteretRepository $centreInteretRepository,
        private readonly PoleEmploiImportUtils $utils,
    ) {
    }

    /**
     * @param array<string, mixed> $sources
     * @param array<string, int>   $resume
     */
    public function importer(array $sources, ImportContext $contexte, array &$resume): void
    {
        foreach ($this->centreInteretRepository->findAll() as $centreInteret) {
            $cle = $this->utils->normaliserCleTexte($centreInteret->getLibelle());
            if (null !== $cle) {
                $contexte->centresInteretParCle[$cle] = $centreInteret;
            }
        }

        foreach ($sources['arbo_centre_interet'] as $ligneCentreInteret) {
            $libelle = $this->utils->normaliserTexte($ligneCentreInteret['libelle_centre_interet'] ?? null);
            if (null === $libelle) {
                ++$resume['ignored'];
                continue;
            }

            $cle = $this->utils->normaliserCleTexte($libelle);
            if (null === $cle) {
                ++$resume['ignored'];
                continue;
            }

            $centreInteret = $contexte->centresInteretParCle[$cle] ?? null;
            if (null === $centreInteret) {
                $centreInteret = new CentreInteret();
                $this->entityManager->persist($centreInteret);
                $contexte->centresInteretParCle[$cle] = $centreInteret;
                ++$resume['created'];
            } else {
                ++$resume['updated'];
            }

            $centreInteret
                ->setLibelle($libelle)
                ->setDefinition($this->utils->normaliserTexte($ligneCentreInteret['definition_centre_interet'] ?? null));

            foreach (($ligneCentreInteret['liste_metier'] ?? []) as $metierLiaison) {
                $codeRome = $this->utils->normaliserCode($metierLiaison['code_rome'] ?? null);
                if (null === $codeRome) {
                    continue;
                }

                $contexte->liaisonsCentreInteretParRome[$codeRome][$cle] = [
                    'centre_interet' => $centreInteret,
                    'principal' => $this->utils->normaliserBooleen($metierLiaison['principal'] ?? null),
                ];
            }
        }

        $this->entityManager->flush();
    }
}
