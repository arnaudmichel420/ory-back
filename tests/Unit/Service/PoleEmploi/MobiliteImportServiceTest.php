<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\PoleEmploi;

use App\Entity\Mobilite;
use App\Repository\MobiliteRepository;
use App\Service\PoleEmploi\ImportContext;
use App\Service\PoleEmploi\MobiliteImportService;

final class MobiliteImportServiceTest extends PoleEmploiServiceTestCase
{
    public function testSynchroniseLesMobilitesEtStockeLaCibleEnString(): void
    {
        $persisted = [];
        $removed = [];
        $entityManager = $this->createEntityManagerMock($persisted, $removed, 1, 1, 0);

        $source = $this->createMetier('1', 'A1001');
        $target = $this->createMetier('2', 'A1002');
        $existing = (new Mobilite())
            ->setCodeOgrMetierSource($source)
            ->setCodeOgrMetierCible('2');
        $toDelete = (new Mobilite())
            ->setCodeOgrMetierSource($source)
            ->setCodeOgrMetierCible('99');

        $repository = $this->createStub(MobiliteRepository::class);
        $repository->method('findAll')->willReturn([$existing, $toDelete]);

        $service = new MobiliteImportService($entityManager, $repository, new \App\Service\PoleEmploi\PoleEmploiImportUtils());
        $contexte = new ImportContext();
        $contexte->metiersParCodeRome = [
            'A1001' => $source,
            'A1002' => $target,
        ];
        $contexte->fichesParRome['A1001'] = [
            'mobilites' => [
                ['rome_cible' => 'A1002 - Cible', 'code_org_rome_cible' => 999, 'ordre_mobilite' => 1],
                ['rome_cible' => 'X9999 - Inconnue', 'code_org_rome_cible' => 300, 'ordre_mobilite' => 2],
            ],
        ];
        $resume = ['created' => 0, 'updated' => 0, 'deleted' => 0, 'ignored' => 0];

        $service->importer([], $contexte, $resume);

        $this->assertSame(['created' => 1, 'updated' => 1, 'deleted' => 1, 'ignored' => 0], $resume);
        $this->assertSame('2', $existing->getCodeOgrMetierCible());
        $this->assertSame('300', $persisted[0]->getCodeOgrMetierCible());
    }
}
