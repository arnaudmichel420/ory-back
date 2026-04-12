<?php

declare(strict_types=1);

namespace App\Service\PoleEmploi;

use App\Entity\Competence;
use App\Repository\CompetenceRepository;
use Doctrine\ORM\EntityManagerInterface;

final class CompetenceImportService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CompetenceRepository $competenceRepository,
        private readonly PoleEmploiImportUtils $utils,
    ) {
    }

    /**
     * @param array<string, mixed> $sources
     * @param array<string, int> $resume
     */
    public function importer(array $sources, ImportContext $contexte, array &$resume): void
    {
        foreach ($this->competenceRepository->findAll() as $competence) {
            $code = $competence->getCodeOgr();
            if (null !== $code) {
                $contexte->competencesParCode[$code] = $competence;
            }
        }

        foreach ($sources['referentiel_competence'] as $ligneCompetence) {
            $this->synchroniserCompetence($ligneCompetence, $contexte, $resume);
        }

        foreach ($sources['referentiel_savoir'] as $ligneSavoir) {
            $this->synchroniserCompetence($ligneSavoir, $contexte, $resume);
        }

        $this->entityManager->flush();
    }

    /**
     * @param array<string, mixed> $ligneCompetence
     * @param array<string, int> $resume
     */
    private function synchroniserCompetence(array $ligneCompetence, ImportContext $contexte, array &$resume): void
    {
        $codeOgr = $this->utils->normaliserCode($ligneCompetence['code_ogr'] ?? null);
        $libelle = $this->utils->normaliserTexte($ligneCompetence['libelle'] ?? null);
        $type = $this->utils->determinerTypeCompetence($ligneCompetence['libelle_categorie'] ?? null);

        if (null === $codeOgr || null === $libelle || null === $type) {
            ++$resume['ignored'];
            return;
        }

        $competence = $contexte->competencesParCode[$codeOgr] ?? null;
        if (null === $competence) {
            $competence = new Competence();
            $competence->setCodeOgr($codeOgr);
            $this->entityManager->persist($competence);
            $contexte->competencesParCode[$codeOgr] = $competence;
            ++$resume['created'];
        } else {
            ++$resume['updated'];
        }

        $competence
            ->setLibelle($libelle)
            ->setType($type)
            ->setTransitionEco($this->utils->normaliserTransitionEco($ligneCompetence['transition_eco'] ?? null))
            ->setTransitionNum($this->utils->normaliserOuiNon($ligneCompetence['transition_num'] ?? null));
    }
}
