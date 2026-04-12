<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\PoleEmploi;

use App\Entity\Secteur;
use App\Repository\SecteurRepository;
use App\Service\PoleEmploi\ImportContext;
use App\Service\PoleEmploi\SecteurImportService;

final class SecteurImportServiceTest extends PoleEmploiServiceTestCase
{
    public function testCreeMetAJourLesSecteursEtSousSecteurs(): void
    {
        $persisted = [];
        $removed = [];
        $entityManager = $this->createEntityManagerMock($persisted, $removed, 2, 0, 1);

        $existing = $this->createSecteur('100');
        $repository = $this->createStub(SecteurRepository::class);
        $repository->method('findAll')->willReturn([$existing]);

        $service = new SecteurImportService($entityManager, $repository, new \App\Service\PoleEmploi\PoleEmploiImportUtils());
        $contexte = new ImportContext();
        $resume = ['created' => 0, 'updated' => 0, 'ignored' => 0];

        $service->importer([
            'arbo_secteur' => [
                [
                    'code_secteur' => '100',
                    'libelle' => 'Racine',
                    'definition' => 'Definition 100',
                    'liste_sous_secteur' => [
                        ['code_sous_secteur' => '101', 'libelle' => 'Sous 101', 'definition' => ''],
                    ],
                ],
                [
                    'code_secteur' => '200',
                    'libelle' => 'Autre racine',
                    'definition' => null,
                    'liste_sous_secteur' => [],
                ],
            ],
        ], $contexte, $resume);

        $this->assertSame(['created' => 2, 'updated' => 1, 'ignored' => 0], $resume);
        $secteursParCode = $contexte->secteursParCode;
        $secteur100 = $this->getElementParCle($secteursParCode, '100');
        $secteur101 = $this->getElementParCle($secteursParCode, '101');

        self::assertInstanceOf(Secteur::class, $secteur100);
        self::assertInstanceOf(Secteur::class, $secteur101);
        $this->assertSame($secteur100, $secteur101->getSousSecteurParent());
    }
}
