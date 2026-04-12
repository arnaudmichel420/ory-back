<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\PoleEmploi;

use App\Repository\CentreInteretRepository;
use App\Service\PoleEmploi\CentreInteretImportService;
use App\Service\PoleEmploi\ImportContext;

final class CentreInteretImportServiceTest extends PoleEmploiServiceTestCase
{
    public function testCreeMetAJourEtIndexeLesLiaisonsCentreInteret(): void
    {
        $persisted = [];
        $removed = [];
        $entityManager = $this->createEntityManagerMock($persisted, $removed, 1, 0, 1);

        $existing = $this->createCentreInteret('Analyser');
        $repository = $this->createStub(CentreInteretRepository::class);
        $repository->method('findAll')->willReturn([$existing]);

        $service = new CentreInteretImportService($entityManager, $repository, new \App\Service\PoleEmploi\PoleEmploiImportUtils());
        $contexte = new ImportContext();
        $resume = ['created' => 0, 'updated' => 0, 'ignored' => 0];

        $service->importer([
            'arbo_centre_interet' => [
                [
                    'libelle_centre_interet' => 'Analyser',
                    'definition_centre_interet' => 'Definition analysee',
                    'liste_metier' => [
                        ['code_rome' => 'M1805', 'principal' => true],
                    ],
                ],
                [
                    'libelle_centre_interet' => 'Construire',
                    'definition_centre_interet' => 'Definition construire',
                    'liste_metier' => [
                        ['code_rome' => 'F1602', 'principal' => false],
                    ],
                ],
            ],
        ], $contexte, $resume);

        self::assertSame(['created' => 1, 'updated' => 1, 'ignored' => 0], $resume);
        self::assertArrayHasKey('analyser', $contexte->centresInteretParCle);
        self::assertArrayHasKey('construire', $contexte->centresInteretParCle);
        self::assertTrue($contexte->liaisonsCentreInteretParRome['M1805']['analyser']['principal']);
        self::assertFalse($contexte->liaisonsCentreInteretParRome['F1602']['construire']['principal']);
    }
}
