<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\PoleEmploi;

use App\Repository\DomaineRepository;
use App\Repository\SousDomaineRepository;
use App\Service\PoleEmploi\DomaineSousDomaineImportService;
use App\Service\PoleEmploi\ImportContext;

final class DomaineSousDomaineImportServiceTest extends PoleEmploiServiceTestCase
{
    public function testCreeMetAJourEtIndexeLesDomainesEtSousDomaines(): void
    {
        $persisted = [];
        $removed = [];
        $entityManager = $this->createEntityManagerMock($persisted, $removed, 3, 0, 1);

        $existingDomaine = $this->createDomaine('A');
        $existingSousDomaine = $this->createSousDomaine('A10', $existingDomaine);

        $domaineRepository = $this->createStub(DomaineRepository::class);
        $domaineRepository->method('findAll')->willReturn([$existingDomaine]);

        $sousDomaineRepository = $this->createStub(SousDomaineRepository::class);
        $sousDomaineRepository->method('findAll')->willReturn([$existingSousDomaine]);

        $service = new DomaineSousDomaineImportService(
            $entityManager,
            $domaineRepository,
            $sousDomaineRepository,
            new \App\Service\PoleEmploi\PoleEmploiImportUtils(),
        );

        $contexte = new ImportContext();
        $resumeDomaines = ['created' => 0, 'updated' => 0, 'ignored' => 0];
        $resumeSousDomaines = ['created' => 0, 'updated' => 0, 'ignored' => 0];

        $service->importer([
            'arbo_principale' => [
                [
                    'code_metier' => 'A',
                    'libelle' => 'Domaine A',
                    'liste_domaine_prof' => [
                        [
                            'code_metier' => 'A10',
                            'libelle' => 'Sous A10',
                            'liste_metier' => [['code_rome' => 'A1001']],
                        ],
                        [
                            'code_metier' => 'A11',
                            'libelle' => 'Sous A11',
                            'liste_metier' => [['code_rome' => 'A1101']],
                        ],
                    ],
                ],
                [
                    'code_metier' => 'B',
                    'libelle' => 'Domaine B',
                    'liste_domaine_prof' => [
                        [
                            'code_metier' => 'B10',
                            'libelle' => 'Sous B10',
                            'liste_metier' => [['code_rome' => 'B1001']],
                        ],
                    ],
                ],
            ],
        ], $contexte, $resumeDomaines, $resumeSousDomaines);

        $this->assertSame(['created' => 1, 'updated' => 1, 'ignored' => 0], $resumeDomaines);
        $this->assertSame(['created' => 2, 'updated' => 1, 'ignored' => 0], $resumeSousDomaines);
        $this->assertSame('A10', $contexte->codeSousDomaineParRome['A1001']);
        $this->assertSame('A11', $contexte->codeSousDomaineParRome['A1101']);
        $this->assertSame('B10', $contexte->codeSousDomaineParRome['B1001']);
    }
}
