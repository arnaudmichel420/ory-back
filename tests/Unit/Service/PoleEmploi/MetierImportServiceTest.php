<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\PoleEmploi;

use App\Repository\MetierRepository;
use App\Service\PoleEmploi\ImportContext;
use App\Service\PoleEmploi\MetierImportService;

final class MetierImportServiceTest extends PoleEmploiServiceTestCase
{
    public function testCreeEtMetAJourLesMetiersParCodeRomeExact(): void
    {
        $persisted = [];
        $removed = [];
        $entityManager = $this->createEntityManagerMock($persisted, $removed, 1, 0, 1);

        $domaine = $this->createDomaine('A');
        $sousDomaine = $this->createSousDomaine('A10', $domaine);
        $existing = $this->createMetier('1', 'A1001');

        $repository = $this->createStub(MetierRepository::class);
        $repository->method('findAll')->willReturn([$existing]);

        $service = new MetierImportService($entityManager, $repository, new \App\Service\PoleEmploi\PoleEmploiImportUtils());
        $contexte = new ImportContext();
        $contexte->sousDomainesParCode['A10'] = $sousDomaine;
        $contexte->codeSousDomaineParRome['A1001'] = 'A10';
        $contexte->codeSousDomaineParRome['A1002'] = 'A10';
        $contexte->fichesParRome = [
            'A1001' => [
                'rome' => ['intitule' => 'Intitulé fiche 1'],
                'definition' => 'Définition 1',
                'acces_metier' => 'Accès 1',
            ],
            'A1002' => [
                'rome' => ['intitule' => 'Intitulé fiche 2'],
                'definition' => 'Définition 2',
                'acces_metier' => 'Accès 2',
            ],
        ];
        $resume = ['created' => 0, 'updated' => 0, 'ignored' => 0];

        $service->importer([
            'referentiel_code_rome' => [
                ['code_ogr' => '1', 'code_rome' => 'A1001', 'libelle' => 'Libelle 1', 'transition_eco' => 'Emploi Vert', 'transition_num' => 'O', 'emploi_reglemente' => 'N', 'emploi_cadre' => 'O'],
                ['code_ogr' => '2', 'code_rome' => 'A1002', 'libelle' => 'Libelle 2', 'transition_eco' => null, 'transition_num' => 'N', 'emploi_reglemente' => 'O', 'emploi_cadre' => 'N'],
            ],
        ], $contexte, $resume);

        self::assertSame(['created' => 1, 'updated' => 1, 'ignored' => 0], $resume);
        self::assertSame('Définition 1', $contexte->metiersParCodeRome['A1001']->getDefinition());
        self::assertSame('Intitulé fiche 2', $contexte->metiersParCodeRome['A1002']->getLibelle());
        self::assertSame($sousDomaine, $contexte->metiersParCodeRome['A1002']->getSousDomaine());
    }
}
