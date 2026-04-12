<?php

declare(strict_types=1);

namespace App\Service\PoleEmploi;

use App\Entity\MetierSecteur;
use App\Repository\MetierSecteurRepository;
use Doctrine\ORM\EntityManagerInterface;

class MetierSecteurImportService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MetierSecteurRepository $metierSecteurRepository,
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
        foreach ($this->metierSecteurRepository->findAll() as $liaison) {
            $codeMetier = $liaison->getCodeOgrMetier()?->getCodeOgr();
            $codeSecteur = $liaison->getSecteur()?->getCode();
            if (null !== $codeMetier && null !== $codeSecteur) {
                $existantesParMetier[$codeMetier][$codeSecteur] = $liaison;
            }
        }

        foreach ($contexte->metiersParCodeRome as $codeRome => $metier) {
            $codeMetier = $metier->getCodeOgr();
            $fiche = $contexte->fichesParRome[$codeRome] ?? null;

            if (null === $codeMetier || null === $fiche) {
                continue;
            }

            $desirees = [];
            foreach (($fiche['secteurs_activite'] ?? []) as $secteurSource) {
                $codeSecteur = $this->utils->normaliserCode($secteurSource['code'] ?? null);
                if (null !== $codeSecteur) {
                    $desirees[$codeSecteur] = [
                        'principal' => $this->utils->normaliserBooleen($secteurSource['principal'] ?? null),
                    ];
                }
            }

            $existantes = $existantesParMetier[$codeMetier] ?? [];

            foreach ($desirees as $codeSecteur => $donnees) {
                $secteur = $contexte->secteursParCode[$codeSecteur] ?? null;
                if (null === $secteur) {
                    ++$resume['ignored'];
                    continue;
                }

                $liaison = $existantes[$codeSecteur] ?? null;
                if (null === $liaison) {
                    $liaison = new MetierSecteur();
                    $this->entityManager->persist($liaison);
                    ++$resume['created'];
                } else {
                    ++$resume['updated'];
                }

                $liaison
                    ->setCodeOgrMetier($metier)
                    ->setSecteur($secteur)
                    ->setPrincipal($donnees['principal']);
            }

            foreach ($existantes as $codeSecteur => $liaison) {
                if (!isset($desirees[$codeSecteur])) {
                    $this->entityManager->remove($liaison);
                    ++$resume['deleted'];
                }
            }
        }
    }
}
