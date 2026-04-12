<?php

declare(strict_types=1);

namespace App\Service\PoleEmploi;

use App\Entity\Secteur;
use App\Repository\SecteurRepository;
use Doctrine\ORM\EntityManagerInterface;

class SecteurImportService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SecteurRepository $secteurRepository,
        private readonly PoleEmploiImportUtils $utils,
    ) {
    }

    /**
     * @param array<string, mixed> $sources
     * @param array<string, int>   $resume
     */
    public function importer(array $sources, ImportContext $contexte, array &$resume): void
    {
        foreach ($this->secteurRepository->findAll() as $secteur) {
            $code = $secteur->getCode();
            if (null !== $code) {
                $contexte->secteursParCode[$code] = $secteur;
            }
        }

        foreach ($sources['arbo_secteur'] as $ligneSecteur) {
            $codeSecteur = $this->utils->normaliserCode($ligneSecteur['code_secteur'] ?? null);
            $libelleSecteur = $this->utils->normaliserTexte($ligneSecteur['libelle'] ?? null);

            if (null === $codeSecteur || null === $libelleSecteur) {
                ++$resume['ignored'];
                continue;
            }

            $secteur = $contexte->secteursParCode[$codeSecteur] ?? null;
            if (null === $secteur) {
                $secteur = new Secteur();
                $secteur->setCode($codeSecteur);
                $this->entityManager->persist($secteur);
                $contexte->secteursParCode[$codeSecteur] = $secteur;
                ++$resume['created'];
            } else {
                ++$resume['updated'];
            }

            $secteur
                ->setLibelle($libelleSecteur)
                ->setDefinition($this->utils->normaliserTexte($ligneSecteur['definition'] ?? null) ?? '')
                ->setSousSecteurParent(null);

            foreach (($ligneSecteur['liste_sous_secteur'] ?? []) as $ligneSousSecteur) {
                $codeSousSecteur = $this->utils->normaliserCode($ligneSousSecteur['code_sous_secteur'] ?? null);
                $libelleSousSecteur = $this->utils->normaliserTexte($ligneSousSecteur['libelle'] ?? null);

                if (null === $codeSousSecteur || null === $libelleSousSecteur) {
                    ++$resume['ignored'];
                    continue;
                }

                $sousSecteur = $contexte->secteursParCode[$codeSousSecteur] ?? null;
                if (null === $sousSecteur) {
                    $sousSecteur = new Secteur();
                    $sousSecteur->setCode($codeSousSecteur);
                    $this->entityManager->persist($sousSecteur);
                    $contexte->secteursParCode[$codeSousSecteur] = $sousSecteur;
                    ++$resume['created'];
                } else {
                    ++$resume['updated'];
                }

                $sousSecteur
                    ->setLibelle($libelleSousSecteur)
                    ->setDefinition($this->utils->normaliserTexte($ligneSousSecteur['definition'] ?? null) ?? '')
                    ->setSousSecteurParent($secteur);
            }
        }

        $this->entityManager->flush();
    }
}
