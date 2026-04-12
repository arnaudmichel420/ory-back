<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\PoleEmploi;

use App\Entity\ContexteTravail;
use App\Repository\ContexteTravailRepository;
use App\Service\PoleEmploi\ContexteTravailImportService;
use App\Service\PoleEmploi\ImportContext;

final class ContexteTravailImportServiceTest extends PoleEmploiServiceTestCase
{
    public function testCreeEtMetAJourLesContextesDeTravail(): void
    {
        $persisted = [];
        $removed = [];
        $entityManager = $this->createEntityManagerMock($persisted, $removed, 1, 0, 1);

        $existing = $this->createContexteTravail('10');
        $repository = $this->createStub(ContexteTravailRepository::class);
        $repository->method('findAll')->willReturn([$existing]);

        $service = new ContexteTravailImportService($entityManager, $repository, new \App\Service\PoleEmploi\PoleEmploiImportUtils());
        $contexte = new ImportContext();
        $resume = ['created' => 0, 'updated' => 0, 'ignored' => 0];

        $service->importer([
            'referentiel_contexte' => [
                ['code_ogr' => '10', 'libelle' => 'Contexte 10', 'type_contexte' => 'Horaires'],
                ['code_ogr' => '20', 'libelle' => 'Contexte 20', 'type_contexte' => 'Rythme'],
            ],
        ], $contexte, $resume);

        $this->assertSame(['created' => 1, 'updated' => 1, 'ignored' => 0], $resume);
        $contextesTravailParCode = $contexte->contextesTravailParCode;
        $contexte10 = $this->getElementParCle($contextesTravailParCode, '10');

        self::assertInstanceOf(ContexteTravail::class, $contexte10);
        $this->assertSame('Horaires', $contexte10->getTypeContexte());
    }
}
