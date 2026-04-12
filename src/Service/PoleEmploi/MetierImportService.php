<?php

declare(strict_types=1);

namespace App\Service\PoleEmploi;

use App\Entity\Metier;
use App\Repository\MetierRepository;
use Doctrine\ORM\EntityManagerInterface;

final class MetierImportService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MetierRepository $metierRepository,
        private readonly PoleEmploiImportUtils $utils,
    ) {
    }

    /**
     * @param array<string, mixed> $sources
     * @param array<string, int> $resume
     */
    public function importer(array $sources, ImportContext $contexte, array &$resume): void
    {
        foreach ($this->metierRepository->findAll() as $metier) {
            $codeRome = $metier->getCodeRome();
            if (null !== $codeRome) {
                $contexte->metiersParCodeRome[$codeRome] = $metier;
            }
        }

        foreach ($sources['referentiel_code_rome'] as $ligneMetier) {
            $codeRome = $this->utils->normaliserCode($ligneMetier['code_rome'] ?? null);
            $libelle = $this->utils->normaliserTexte($ligneMetier['libelle'] ?? null);
            $codeOgrSource = $this->utils->normaliserCode($ligneMetier['code_ogr'] ?? null);

            if (null === $codeRome || null === $libelle || null === $codeOgrSource) {
                ++$resume['ignored'];
                continue;
            }

            $fiche = $contexte->fichesParRome[$codeRome] ?? null;
            $codeSousDomaine = $contexte->codeSousDomaineParRome[$codeRome] ?? null;

            if (null === $codeSousDomaine) {
                ++$resume['ignored'];
                continue;
            }

            $sousDomaine = $contexte->sousDomainesParCode[$codeSousDomaine] ?? null;
            if (null === $sousDomaine) {
                ++$resume['ignored'];
                continue;
            }

            $metier = $contexte->metiersParCodeRome[$codeRome] ?? null;
            if (null === $metier) {
                $metier = new Metier();
                $metier
                    ->setCodeOgr($codeOgrSource)
                    ->setCodeRome($codeRome);
                $this->entityManager->persist($metier);
                $contexte->metiersParCodeRome[$codeRome] = $metier;
                ++$resume['created'];
            } else {
                ++$resume['updated'];
            }

            $metier
                ->setLibelle($this->utils->normaliserTexte($fiche['rome']['intitule'] ?? $libelle) ?? $libelle)
                ->setDefinition($this->utils->normaliserTexte($fiche['definition'] ?? null))
                ->setAccesMetier($this->utils->normaliserTexte($fiche['acces_metier'] ?? null))
                ->setTransitionEco($this->utils->normaliserTransitionEco($ligneMetier['transition_eco'] ?? null))
                ->setTransitionNum($this->utils->normaliserOuiNon($ligneMetier['transition_num'] ?? null))
                ->setEmploiReglemente($this->utils->normaliserOuiNon($ligneMetier['emploi_reglemente'] ?? null))
                ->setEmploiCadre($this->utils->normaliserOuiNon($ligneMetier['emploi_cadre'] ?? null))
                ->setSousDomaine($sousDomaine);
        }

        $this->entityManager->flush();
    }
}
