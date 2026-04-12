<?php

declare(strict_types=1);

namespace App\Service\PoleEmploi;

use App\Entity\MetierContexteTravail;
use App\Repository\MetierContexteTravailRepository;
use Doctrine\ORM\EntityManagerInterface;

class MetierContexteTravailImportService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MetierContexteTravailRepository $metierContexteTravailRepository,
        private readonly PoleEmploiImportUtils $utils,
    ) {
    }

    /**
     * @param array<string, mixed> $sources
     * @param array<string, int>   $resume
     */
    public function importer(array $sources, ImportContext $contexte, array &$resume): void
    {
        $existantesParMetier = [];
        foreach ($this->metierContexteTravailRepository->findAll() as $liaison) {
            $codeMetier = $liaison->getCodeOgrMetier()?->getCodeOgr();
            $codeContexte = $liaison->getCodeOgrContexte()?->getCodeOgr();
            if (null !== $codeMetier && null !== $codeContexte) {
                $existantesParMetier[$codeMetier][$codeContexte] = $liaison;
            }
        }

        foreach ($contexte->metiersParCodeRome as $codeRome => $metier) {
            $codeMetier = $metier->getCodeOgr();
            $fiche = $contexte->fichesParRome[$codeRome] ?? null;

            if (null === $codeMetier || null === $fiche) {
                continue;
            }

            $desirees = [];
            foreach (($fiche['contextes_travail'] ?? []) as $groupe) {
                $libelleGroupe = $this->utils->normaliserTexte($groupe['libelle'] ?? null);

                foreach (($groupe['items'] ?? []) as $item) {
                    $codeContexte = $this->utils->normaliserCode($item['code_ogr'] ?? null);
                    if (null !== $codeContexte) {
                        $desirees[$codeContexte] = [
                            'libelle_groupe' => $libelleGroupe,
                        ];
                    }
                }
            }

            $existantes = $existantesParMetier[$codeMetier] ?? [];

            foreach ($desirees as $codeContexte => $donnees) {
                $contexteTravail = $contexte->contextesTravailParCode[$codeContexte] ?? null;
                if (null === $contexteTravail) {
                    ++$resume['ignored'];
                    continue;
                }

                $liaison = $existantes[$codeContexte] ?? null;
                if (null === $liaison) {
                    $liaison = new MetierContexteTravail();
                    $this->entityManager->persist($liaison);
                    ++$resume['created'];
                } else {
                    ++$resume['updated'];
                }

                $liaison
                    ->setCodeOgrMetier($metier)
                    ->setCodeOgrContexte($contexteTravail)
                    ->setLibelleGroupe($donnees['libelle_groupe']);
            }

            foreach ($existantes as $codeContexte => $liaison) {
                if (!isset($desirees[$codeContexte])) {
                    $this->entityManager->remove($liaison);
                    ++$resume['deleted'];
                }
            }
        }
    }
}
