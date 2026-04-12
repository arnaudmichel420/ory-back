<?php

declare(strict_types=1);

namespace App\Service\PoleEmploi;

use App\Entity\MetierCentreInteret;
use App\Repository\MetierCentreInteretRepository;
use Doctrine\ORM\EntityManagerInterface;

final class MetierCentreInteretImportService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MetierCentreInteretRepository $metierCentreInteretRepository,
        private readonly PoleEmploiImportUtils $utils,
    ) {
    }

    /**
     * @param array<string, mixed> $sources
     * @param array<string, int> $resume
     */
    public function importer(array $sources, ImportContext $contexte, array &$resume): void
    {
        $existantesParMetier = [];
        foreach ($this->metierCentreInteretRepository->findAll() as $liaison) {
            $codeMetier = $liaison->getCodeOgrMetier()?->getCodeOgr();
            $cleCentreInteret = $this->utils->normaliserCleTexte($liaison->getCentreInteret()?->getLibelle());
            if (null !== $codeMetier && null !== $cleCentreInteret) {
                $existantesParMetier[$codeMetier][$cleCentreInteret] = $liaison;
            }
        }

        foreach ($contexte->metiersParCodeRome as $codeRome => $metier) {
            $codeMetier = $metier->getCodeOgr();
            if (null === $codeMetier) {
                continue;
            }

            $desirees = $contexte->liaisonsCentreInteretParRome[$codeRome] ?? [];
            $existantes = $existantesParMetier[$codeMetier] ?? [];

            foreach ($desirees as $cleCentreInteret => $donnees) {
                $liaison = $existantes[$cleCentreInteret] ?? null;

                if (null === $liaison) {
                    $liaison = new MetierCentreInteret();
                    $this->entityManager->persist($liaison);
                    ++$resume['created'];
                } else {
                    ++$resume['updated'];
                }

                $liaison
                    ->setCodeOgrMetier($metier)
                    ->setCentreInteret($donnees['centre_interet'])
                    ->setPrincipal($donnees['principal']);
            }

            foreach ($existantes as $cleCentreInteret => $liaison) {
                if (!isset($desirees[$cleCentreInteret])) {
                    $this->entityManager->remove($liaison);
                    ++$resume['deleted'];
                }
            }
        }
    }
}
