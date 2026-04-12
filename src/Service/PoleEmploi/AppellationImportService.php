<?php

declare(strict_types=1);

namespace App\Service\PoleEmploi;

use App\Entity\Appellation;
use App\Repository\AppellationRepository;
use Doctrine\ORM\EntityManagerInterface;

final class AppellationImportService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AppellationRepository $appellationRepository,
        private readonly PoleEmploiImportUtils $utils,
    ) {}

    /**
     * @param array<string, mixed> $sources
     * @param array<string, int> $resume
     */
    public function importer(array $sources, ImportContext $contexte, array &$resume): void
    {
        $appellationsParRome = $this->utils->indexerAppellationsParCodeRome(
            $sources['referentiel_appellation'],
            $contexte->fichesParRome,
        );

        $existantesParCodeOgr = [];
        $existantesParMetier = [];
        foreach ($this->appellationRepository->findAll() as $appellation) {
            $codeMetier = $appellation->getCodeOgrMetier()?->getCodeOgr();
            $codeOgr = $appellation->getCodeOgr();
            if (null !== $codeOgr) {
                $existantesParCodeOgr[$codeOgr] = $appellation;
            }

            if (null !== $codeMetier && null !== $codeOgr) {
                $existantesParMetier[$codeMetier][$codeOgr] = $appellation;
            }
        }

        foreach ($contexte->metiersParCodeRome as $codeRome => $metier) {
            $codeMetier = $metier->getCodeOgr();
            if (null === $codeMetier) {
                ++$resume['ignored'];
                continue;
            }

            $desirees = $appellationsParRome[$codeRome] ?? [];
            $existantes = $existantesParMetier[$codeMetier] ?? [];
            $clesDesirees = [];

            foreach ($desirees as $codeOgr => $donnees) {
                $codeOgrNormalise = $donnees['code_ogr'];
                $clesDesirees[$codeOgrNormalise] = true;
                $appellation = $existantes[$codeOgrNormalise] ?? $existantesParCodeOgr[$codeOgrNormalise] ?? null;

                if (null === $appellation) {
                    $appellation = new Appellation();
                    $appellation->setCodeOgr($codeOgrNormalise);
                    $this->entityManager->persist($appellation);
                    $existantesParCodeOgr[$codeOgrNormalise] = $appellation;
                    ++$resume['created'];
                } else {
                    ++$resume['updated'];
                }

                $appellation
                    ->setLibelle($donnees['libelle'])
                    ->setLibelleCourt($donnees['libelle_court'])
                    ->setCodeOgrMetier($metier);

                if (\is_bool($donnees['peu_utiliser'])) {
                    $appellation->setPeuUtiliser($donnees['peu_utiliser']);
                }
            }

            foreach ($existantes as $codeOgr => $appellation) {
                if (!isset($clesDesirees[$codeOgr])) {
                    $this->entityManager->remove($appellation);
                    ++$resume['deleted'];
                }
            }
        }
    }
}
