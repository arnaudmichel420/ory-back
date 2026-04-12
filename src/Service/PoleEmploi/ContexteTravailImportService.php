<?php

declare(strict_types=1);

namespace App\Service\PoleEmploi;

use App\Entity\ContexteTravail;
use App\Repository\ContexteTravailRepository;
use Doctrine\ORM\EntityManagerInterface;

class ContexteTravailImportService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ContexteTravailRepository $contexteTravailRepository,
        private readonly PoleEmploiImportUtils $utils,
    ) {
    }

    /**
     * @param array<string, mixed> $sources
     * @param array<string, int>   $resume
     */
    public function importer(array $sources, ImportContext $contexte, array &$resume): void
    {
        foreach ($this->contexteTravailRepository->findAll() as $contexteTravail) {
            $code = $contexteTravail->getCodeOgr();
            if (null !== $code) {
                $contexte->contextesTravailParCode[$code] = $contexteTravail;
            }
        }

        foreach ($sources['referentiel_contexte'] as $ligneContexte) {
            $codeOgr = $this->utils->normaliserCode($ligneContexte['code_ogr'] ?? null);
            $libelle = $this->utils->normaliserTexte($ligneContexte['libelle'] ?? null);

            if (null === $codeOgr || null === $libelle) {
                ++$resume['ignored'];
                continue;
            }

            $contexteTravail = $contexte->contextesTravailParCode[$codeOgr] ?? null;
            if (null === $contexteTravail) {
                $contexteTravail = new ContexteTravail();
                $contexteTravail->setCodeOgr($codeOgr);
                $this->entityManager->persist($contexteTravail);
                $contexte->contextesTravailParCode[$codeOgr] = $contexteTravail;
                ++$resume['created'];
            } else {
                ++$resume['updated'];
            }

            $contexteTravail
                ->setLibelle($libelle)
                ->setTypeContexte($this->utils->normaliserTexte($ligneContexte['type_contexte'] ?? null));
        }

        $this->entityManager->flush();
    }
}
