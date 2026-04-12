<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\PoleEmploi;

use App\Entity\MetierCompetence;
use App\Enum\MetierCompetenceTypeEnum;
use App\Repository\MetierCompetenceRepository;
use App\Service\PoleEmploi\ImportContext;
use App\Service\PoleEmploi\MetierCompetenceImportService;

final class MetierCompetenceImportServiceTest extends PoleEmploiServiceTestCase
{
    public function testSynchroniseLesLiaisonsMetierCompetence(): void
    {
        $persisted = [];
        $removed = [];
        $entityManager = $this->createEntityManagerMock($persisted, $removed, 1, 1, 0);

        $metier = $this->createMetier('1', 'A1001');
        $competenceExistante = $this->createCompetence('10', MetierCompetenceTypeEnum::SAVOIR_FAIRE);
        $existing = (new MetierCompetence())
            ->setCodeOgrMetier($metier)
            ->setCodeOgrComp($competenceExistante)
            ->setType(MetierCompetenceTypeEnum::SAVOIR_FAIRE);
        $toDelete = (new MetierCompetence())
            ->setCodeOgrMetier($metier)
            ->setCodeOgrComp($this->createCompetence('99', MetierCompetenceTypeEnum::SAVOIR))
            ->setType(MetierCompetenceTypeEnum::SAVOIR);

        $repository = $this->createStub(MetierCompetenceRepository::class);
        $repository->method('findAll')->willReturn([$existing, $toDelete]);

        $service = new MetierCompetenceImportService($entityManager, $repository, new \App\Service\PoleEmploi\PoleEmploiImportUtils());
        $contexte = new ImportContext();
        $code10 = '10';
        $code20 = '20';

        $contexte->metiersParCodeRome['A1001'] = $metier;
        $contexte->competencesParCode = [];
        $this->ajouterCompetenceAuContexte($contexte, $code10, $competenceExistante);
        $this->ajouterCompetenceAuContexte($contexte, $code20, $this->createCompetence($code20, MetierCompetenceTypeEnum::SAVOIR));
        $contexte->fichesParRome = [
            'A1001' => [
                'competences' => [
                    'savoir_faire' => ['enjeux' => [['libelle' => 'Prod', 'items' => [['code_ogr' => '10', 'coeur_metier' => 'Principale']]]]],
                    'savoir_etre_professionnel' => ['enjeux' => []],
                    'savoirs' => ['categories' => [['libelle' => 'Normes', 'items' => [['code_ogr' => '20', 'coeur_metier' => 'émergente']]]]],
                ],
            ],
        ];
        $resume = ['created' => 0, 'updated' => 0, 'deleted' => 0, 'ignored' => 0];

        $service->importer([], $contexte, $resume);

        self::assertSame(['created' => 1, 'updated' => 1, 'deleted' => 1, 'ignored' => 0], $resume);
        self::assertSame(2, $existing->getCoeurMetier());
        self::assertSame('Prod', $existing->getLibelleEnjeu());
    }
}
