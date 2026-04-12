<?php

declare(strict_types=1);

namespace App\Service\PoleEmploi;

use App\Entity\Domaine;
use App\Entity\SousDomaine;
use App\Repository\DomaineRepository;
use App\Repository\SousDomaineRepository;
use Doctrine\ORM\EntityManagerInterface;

class DomaineSousDomaineImportService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DomaineRepository $domaineRepository,
        private readonly SousDomaineRepository $sousDomaineRepository,
        private readonly PoleEmploiImportUtils $utils,
    ) {
    }

    /**
     * @param array<string, mixed> $sources
     * @param array<string, int>   $resumeDomaines
     * @param array<string, int>   $resumeSousDomaines
     */
    public function importer(array $sources, ImportContext $contexte, array &$resumeDomaines, array &$resumeSousDomaines): void
    {
        foreach ($this->domaineRepository->findAll() as $domaine) {
            $code = $domaine->getCode();
            if (null !== $code) {
                $contexte->domainesParCode[$code] = $domaine;
            }
        }

        foreach ($this->sousDomaineRepository->findAll() as $sousDomaine) {
            $code = $sousDomaine->getCode();
            if (null !== $code) {
                $contexte->sousDomainesParCode[$code] = $sousDomaine;
            }
        }

        foreach ($sources['arbo_principale'] as $famille) {
            $codeDomaine = $this->utils->normaliserCode($famille['code_metier'] ?? null);
            $libelleDomaine = $this->utils->normaliserTexte($famille['libelle'] ?? null);

            if (null === $codeDomaine || null === $libelleDomaine) {
                ++$resumeDomaines['ignored'];
                continue;
            }

            $domaine = $contexte->domainesParCode[$codeDomaine] ?? null;
            if (null === $domaine) {
                $domaine = new Domaine();
                $domaine->setCode($codeDomaine);
                $this->entityManager->persist($domaine);
                $contexte->domainesParCode[$codeDomaine] = $domaine;
                ++$resumeDomaines['created'];
            } else {
                ++$resumeDomaines['updated'];
            }

            $domaine->setLibelle($libelleDomaine);

            foreach (($famille['liste_domaine_prof'] ?? []) as $domaineProfessionnel) {
                $codeSousDomaine = $this->utils->normaliserCode($domaineProfessionnel['code_metier'] ?? null);
                $libelleSousDomaine = $this->utils->normaliserTexte($domaineProfessionnel['libelle'] ?? null);

                if (null === $codeSousDomaine || null === $libelleSousDomaine) {
                    ++$resumeSousDomaines['ignored'];
                    continue;
                }

                $sousDomaine = $contexte->sousDomainesParCode[$codeSousDomaine] ?? null;
                if (null === $sousDomaine) {
                    $sousDomaine = new SousDomaine();
                    $sousDomaine->setCode($codeSousDomaine);
                    $this->entityManager->persist($sousDomaine);
                    $contexte->sousDomainesParCode[$codeSousDomaine] = $sousDomaine;
                    ++$resumeSousDomaines['created'];
                } else {
                    ++$resumeSousDomaines['updated'];
                }

                $sousDomaine
                    ->setLibelle($libelleSousDomaine)
                    ->setDomaine($domaine);

                foreach (($domaineProfessionnel['liste_metier'] ?? []) as $metier) {
                    $codeRome = $this->utils->normaliserCode($metier['code_rome'] ?? null);
                    if (null !== $codeRome) {
                        $contexte->codeSousDomaineParRome[$codeRome] = $codeSousDomaine;
                    }
                }
            }
        }

        $this->entityManager->flush();
    }
}
