<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\PoleEmploi;

use App\Entity\Competence;
use App\Enum\MetierCompetenceTypeEnum;
use App\Repository\CompetenceRepository;
use App\Service\PoleEmploi\CompetenceImportService;
use App\Service\PoleEmploi\ImportContext;

final class CompetenceImportServiceTest extends PoleEmploiServiceTestCase
{
    public function testCreeMetAJourEtIgnoreSeulementLesLignesInvalides(): void
    {
        $persisted = [];
        $removed = [];
        $entityManager = $this->createEntityManagerMock($persisted, $removed, 2, 0, 1);

        $existing = $this->createCompetence('10', MetierCompetenceTypeEnum::SAVOIR_FAIRE);
        $repository = $this->createStub(CompetenceRepository::class);
        $repository->method('findAll')->willReturn([$existing]);

        $service = new CompetenceImportService($entityManager, $repository, new \App\Service\PoleEmploi\PoleEmploiImportUtils());
        $contexte = new ImportContext();
        $resume = ['created' => 0, 'updated' => 0, 'ignored' => 0];

        $service->importer([
            'referentiel_competence' => [
                ['code_ogr' => '10', 'libelle' => 'Faire', 'libelle_categorie' => 'Savoir-faire', 'transition_eco' => 'Emploi Vert', 'transition_num' => 'O'],
                ['code_ogr' => '20', 'libelle' => 'Être', 'libelle_categorie' => 'Savoir-être professionnel', 'transition_eco' => null, 'transition_num' => 'N'],
            ],
            'referentiel_savoir' => [
                ['code_ogr' => '30', 'libelle' => 'Savoir', 'libelle_categorie' => 'Produits, outils et matières', 'transition_eco' => null, 'transition_num' => 'N'],
                ['code_ogr' => '40', 'libelle' => 'Invalide', 'libelle_categorie' => 'Categorie inconnue', 'transition_eco' => null, 'transition_num' => null],
            ],
        ], $contexte, $resume);

        $this->assertSame(['created' => 2, 'updated' => 1, 'ignored' => 1], $resume);
        $competencesParCode = $contexte->competencesParCode;
        $competence10 = $this->getElementParCle($competencesParCode, '10');
        $competence30 = $this->getElementParCle($competencesParCode, '30');

        self::assertInstanceOf(Competence::class, $competence10);
        self::assertInstanceOf(Competence::class, $competence30);
        $this->assertSame(MetierCompetenceTypeEnum::SAVOIR, $competence30->getType());
        self::assertTrue($competence10->isTransitionEco());
        self::assertFalse($competence30->isTransitionNum());
    }
}
