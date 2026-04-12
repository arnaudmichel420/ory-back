<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\PoleEmploi;

use App\Repository\AppellationRepository;
use App\Service\PoleEmploi\AppellationImportService;
use App\Service\PoleEmploi\ImportContext;

final class AppellationImportServiceTest extends PoleEmploiServiceTestCase
{
    public function testSynchroniseLesAppellationsSansMelangerLesCodesRomeVoisins(): void
    {
        $persisted = [];
        $removed = [];
        $entityManager = $this->createEntityManagerMock($persisted, $removed, 1, 1, 0);

        $metierA = $this->createMetier('1', 'A1001');
        $metierB = $this->createMetier('2', 'A1002');
        $existingKeep = $this->createAppellation('100', $metierA);
        $existingDelete = $this->createAppellation('200', $metierA);
        $existingForeign = $this->createAppellation('300', $metierB);

        $repository = $this->createStub(AppellationRepository::class);
        $repository->method('findAll')->willReturn([$existingKeep, $existingDelete, $existingForeign]);

        $service = new AppellationImportService($entityManager, $repository, new \App\Service\PoleEmploi\PoleEmploiImportUtils());

        $contexte = new ImportContext();
        $contexte->metiersParCodeRome = [
            'A1001' => $metierA,
            'A1002' => $metierB,
        ];
        $contexte->fichesParRome = [
            'A1001' => [
                'appellations' => [
                    ['code_ogr' => 100, 'libelle' => 'App A', 'libelle_court' => 'Court A'],
                    ['code_ogr' => 400, 'libelle' => 'App New', 'libelle_court' => null],
                ],
            ],
            'A1002' => [
                'appellations' => [
                    ['code_ogr' => 300, 'libelle' => 'App B', 'libelle_court' => 'Court B'],
                ],
            ],
        ];
        $resume = ['created' => 0, 'updated' => 0, 'deleted' => 0, 'ignored' => 0];

        $service->importer([
            'referentiel_appellation' => [
                ['code_ogr' => 100, 'libelle' => 'Ref A', 'libelle_court' => 'Ref court A', 'peu_usite' => 'O'],
                ['code_ogr' => 300, 'libelle' => 'Ref B', 'libelle_court' => 'Ref court B', 'peu_usite' => 'N'],
                ['code_ogr' => 400, 'libelle' => 'Ref New', 'libelle_court' => 'Ref court new', 'peu_usite' => 'N'],
                ['code_ogr' => 500, 'libelle' => 'Voisin parent', 'libelle_court' => 'Voisin', 'peu_usite' => 'N'],
            ],
        ], $contexte, $resume);

        self::assertSame(['created' => 1, 'updated' => 2, 'deleted' => 1, 'ignored' => 0], $resume);
        self::assertSame($metierA, $existingKeep->getCodeOgrMetier());
        self::assertSame($metierB, $existingForeign->getCodeOgrMetier());
        self::assertSame('400', $persisted[0]->getCodeOgr());
        self::assertSame($existingDelete, $removed[0]);
    }
}
