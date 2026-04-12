<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\PoleEmploi;

use App\Entity\MetierCentreInteret;
use App\Repository\MetierCentreInteretRepository;
use App\Service\PoleEmploi\ImportContext;
use App\Service\PoleEmploi\MetierCentreInteretImportService;

final class MetierCentreInteretImportServiceTest extends PoleEmploiServiceTestCase
{
    public function testSynchroniseLesLiaisonsMetierCentreInteret(): void
    {
        $persisted = [];
        $removed = [];
        $entityManager = $this->createEntityManagerMock($persisted, $removed, 1, 1, 0);

        $metier = $this->createMetier('1', 'A1001');
        $existingCentre = $this->createCentreInteret('Analyser');
        $existing = (new MetierCentreInteret())
            ->setCodeOgrMetier($metier)
            ->setCentreInteret($existingCentre);
        $toDelete = (new MetierCentreInteret())
            ->setCodeOgrMetier($metier)
            ->setCentreInteret($this->createCentreInteret('Supprimer'));

        $repository = $this->createStub(MetierCentreInteretRepository::class);
        $repository->method('findAll')->willReturn([$existing, $toDelete]);

        $service = new MetierCentreInteretImportService($entityManager, $repository, new \App\Service\PoleEmploi\PoleEmploiImportUtils());
        $contexte = new ImportContext();
        $contexte->metiersParCodeRome['A1001'] = $metier;
        $contexte->liaisonsCentreInteretParRome['A1001'] = [
            'analyser' => ['centre_interet' => $existingCentre, 'principal' => true],
            'construire' => ['centre_interet' => $this->createCentreInteret('Construire'), 'principal' => false],
        ];
        $resume = ['created' => 0, 'updated' => 0, 'deleted' => 0, 'ignored' => 0];

        $service->importer([], $contexte, $resume);

        self::assertSame(['created' => 1, 'updated' => 1, 'deleted' => 1, 'ignored' => 0], $resume);
        self::assertTrue($existing->isPrincipal());
    }
}
