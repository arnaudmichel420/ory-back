<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\PoleEmploi;

use App\Entity\MetierContexteTravail;
use App\Repository\MetierContexteTravailRepository;
use App\Service\PoleEmploi\ImportContext;
use App\Service\PoleEmploi\MetierContexteTravailImportService;

final class MetierContexteTravailImportServiceTest extends PoleEmploiServiceTestCase
{
    public function testSynchroniseLesLiaisonsMetierContexteTravail(): void
    {
        $persisted = [];
        $removed = [];
        $entityManager = $this->createEntityManagerMock($persisted, $removed, 1, 1, 0);

        $metier = $this->createMetier('1', 'A1001');
        $contexteExistant = $this->createContexteTravail('10');
        $existing = (new MetierContexteTravail())
            ->setCodeOgrMetier($metier)
            ->setCodeOgrContexte($contexteExistant);
        $toDelete = (new MetierContexteTravail())
            ->setCodeOgrMetier($metier)
            ->setCodeOgrContexte($this->createContexteTravail('99'));

        $repository = $this->createStub(MetierContexteTravailRepository::class);
        $repository->method('findAll')->willReturn([$existing, $toDelete]);

        $service = new MetierContexteTravailImportService($entityManager, $repository, new \App\Service\PoleEmploi\PoleEmploiImportUtils());
        $contexte = new ImportContext();
        $code10 = '10';
        $code20 = '20';

        $contexte->metiersParCodeRome['A1001'] = $metier;
        $contexte->contextesTravailParCode = [];
        $this->ajouterContexteTravailAuContexte($contexte, $code10, $contexteExistant);
        $this->ajouterContexteTravailAuContexte($contexte, $code20, $this->createContexteTravail($code20));
        $contexte->fichesParRome['A1001'] = [
            'contextes_travail' => [
                ['libelle' => 'Horaires', 'items' => [['code_ogr' => '10'], ['code_ogr' => '20']]],
            ],
        ];
        $resume = ['created' => 0, 'updated' => 0, 'deleted' => 0, 'ignored' => 0];

        $service->importer([], $contexte, $resume);

        $this->assertSame(['created' => 1, 'updated' => 1, 'deleted' => 1, 'ignored' => 0], $resume);
        $this->assertSame('Horaires', $existing->getLibelleGroupe());
    }
}
