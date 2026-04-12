<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\PoleEmploi;

use App\Entity\MetierSecteur;
use App\Repository\MetierSecteurRepository;
use App\Service\PoleEmploi\ImportContext;
use App\Service\PoleEmploi\MetierSecteurImportService;

final class MetierSecteurImportServiceTest extends PoleEmploiServiceTestCase
{
    public function testSynchroniseLesLiaisonsMetierSecteur(): void
    {
        $persisted = [];
        $removed = [];
        $entityManager = $this->createEntityManagerMock($persisted, $removed, 1, 1, 0);

        $metier = $this->createMetier('1', 'A1001');
        $secteurExistant = $this->createSecteur('10');
        $existing = (new MetierSecteur())
            ->setCodeOgrMetier($metier)
            ->setSecteur($secteurExistant);
        $toDelete = (new MetierSecteur())
            ->setCodeOgrMetier($metier)
            ->setSecteur($this->createSecteur('99'));

        $repository = $this->createStub(MetierSecteurRepository::class);
        $repository->method('findAll')->willReturn([$existing, $toDelete]);

        $service = new MetierSecteurImportService($entityManager, $repository, new \App\Service\PoleEmploi\PoleEmploiImportUtils());
        $contexte = new ImportContext();
        $code10 = '10';
        $code20 = '20';

        $contexte->metiersParCodeRome['A1001'] = $metier;
        $contexte->secteursParCode = [];
        $this->ajouterSecteurAuContexte($contexte, $code10, $secteurExistant);
        $this->ajouterSecteurAuContexte($contexte, $code20, $this->createSecteur($code20));
        $contexte->fichesParRome['A1001'] = [
            'secteurs_activite' => [
                ['code' => '10', 'principal' => true],
                ['code' => '20', 'principal' => false],
            ],
        ];
        $resume = ['created' => 0, 'updated' => 0, 'deleted' => 0, 'ignored' => 0];

        $service->importer([], $contexte, $resume);

        self::assertSame(['created' => 1, 'updated' => 1, 'deleted' => 1, 'ignored' => 0], $resume);
        self::assertTrue($existing->isPrincipal());
    }
}
