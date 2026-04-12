<?php

declare(strict_types=1);

namespace App\Service\PoleEmploi;

use App\Entity\Mobilite;
use App\Repository\MobiliteRepository;
use Doctrine\ORM\EntityManagerInterface;

class MobiliteImportService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MobiliteRepository $mobiliteRepository,
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
        foreach ($this->mobiliteRepository->findAll() as $mobilite) {
            $codeMetier = $mobilite->getCodeOgrMetierSource()?->getCodeOgr();
            $codeCible = $mobilite->getCodeOgrMetierCible();
            if (null !== $codeMetier && null !== $codeCible) {
                $existantesParMetier[$codeMetier][$codeCible] = $mobilite;
            }
        }

        foreach ($contexte->metiersParCodeRome as $codeRome => $metier) {
            $codeMetier = $metier->getCodeOgr();
            $fiche = $contexte->fichesParRome[$codeRome] ?? null;

            if (null === $codeMetier || null === $fiche) {
                continue;
            }

            $desirees = [];
            foreach (($fiche['mobilites'] ?? []) as $mobiliteSource) {
                $codeRomeCible = $this->utils->extraireCodeRomeCible($mobiliteSource['rome_cible'] ?? null);
                $codeCible = null;

                if (null !== $codeRomeCible && isset($contexte->metiersParCodeRome[$codeRomeCible])) {
                    $codeCible = $contexte->metiersParCodeRome[$codeRomeCible]->getCodeOgr();
                }

                if (null === $codeCible) {
                    $codeCible = $this->utils->normaliserCode($mobiliteSource['code_org_rome_cible'] ?? null);
                }

                if (null === $codeCible) {
                    ++$resume['ignored'];
                    continue;
                }

                $codeCibleNormalise = (string) $codeCible;

                $desirees[$codeCibleNormalise] = [
                    'ordre_mobilite' => $this->utils->normaliserEntier($mobiliteSource['ordre_mobilite'] ?? null),
                ];
            }

            $existantes = $existantesParMetier[$codeMetier] ?? [];

            foreach ($desirees as $codeCible => $donnees) {
                $liaison = $existantes[$codeCible] ?? null;
                if (null === $liaison) {
                    $liaison = new Mobilite();
                    $this->entityManager->persist($liaison);
                    ++$resume['created'];
                } else {
                    ++$resume['updated'];
                }

                $liaison
                    ->setCodeOgrMetierSource($metier)
                    ->setCodeOgrMetierCible((string) $codeCible)
                    ->setOrdreMobilite($donnees['ordre_mobilite']);
            }

            foreach ($existantes as $codeCible => $liaison) {
                if (!isset($desirees[$codeCible])) {
                    $this->entityManager->remove($liaison);
                    ++$resume['deleted'];
                }
            }
        }
    }
}
