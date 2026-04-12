<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\PoleEmploi;

use App\Enum\MetierCompetenceTypeEnum;
use App\Service\PoleEmploi\PoleEmploiImportUtils;

final class PoleEmploiImportUtilsTest extends PoleEmploiServiceTestCase
{
    private PoleEmploiImportUtils $utils;

    protected function setUp(): void
    {
        $this->utils = new PoleEmploiImportUtils();
    }

    public function testDeterminerTypeCompetenceReconnaItLesVariantesDeSavoirs(): void
    {
        $this->assertSame(MetierCompetenceTypeEnum::SAVOIR, $this->utils->determinerTypeCompetence('Produits, outils et matières'));
        $this->assertSame(MetierCompetenceTypeEnum::SAVOIR, $this->utils->determinerTypeCompetence('Produits, outils et matires'));
        $this->assertSame(MetierCompetenceTypeEnum::SAVOIR, $this->utils->determinerTypeCompetence('Produits, outils et mati-eres'));
        $this->assertSame(MetierCompetenceTypeEnum::SAVOIR, $this->utils->determinerTypeCompetence('Normes et procédés'));
        $this->assertSame(MetierCompetenceTypeEnum::SAVOIR, $this->utils->determinerTypeCompetence('Normes et procds'));
        $this->assertSame(MetierCompetenceTypeEnum::SAVOIR, $this->utils->determinerTypeCompetence('Normes et proc-ed-es'));
        $this->assertSame(MetierCompetenceTypeEnum::SAVOIR_ETRE_PROFESSIONEL, $this->utils->determinerTypeCompetence('Savoir-être professionnel'));
    }

    public function testNormalisationsConvertissentLesValeursAttandues(): void
    {
        self::assertTrue($this->utils->normaliserOuiNon('O'));
        self::assertFalse($this->utils->normaliserOuiNon('N'));
        self::assertTrue($this->utils->normaliserTransitionEco('Emploi stratégique pour la Transition écologique'));
        self::assertFalse($this->utils->normaliserTransitionEco('Emploi Brun'));
        $this->assertSame(2, $this->utils->normaliserCoeurMetier('Principale'));
        $this->assertSame(1, $this->utils->normaliserCoeurMetier('émergente'));
        $this->assertSame(0, $this->utils->normaliserCoeurMetier(null));
    }

    public function testIndexeLesAppellationsDepuisLesFichesEtLesEnrichitAvecLeReferentiel(): void
    {
        $index = $this->utils->indexerAppellationsParCodeRome(
            [
                [
                    'code_ogr' => 10,
                    'libelle' => 'Libelle référentiel',
                    'libelle_court' => 'Court référentiel',
                    'peu_usite' => 'O',
                ],
                [
                    'code_ogr' => 20,
                    'libelle' => 'Autre référentiel',
                    'libelle_court' => 'Court 20',
                    'peu_usite' => 'N',
                ],
            ],
            [
                'A1001' => [
                    'appellations' => [
                        [
                            'code_ogr' => 10,
                            'libelle' => 'Appellation fiche',
                            'libelle_court' => null,
                        ],
                    ],
                ],
                'A1002' => [
                    'appellations' => [
                        [
                            'code_ogr' => 20,
                            'libelle' => 'Appellation autre fiche',
                            'libelle_court' => 'Court fiche',
                        ],
                    ],
                ],
            ],
        );

        $romeA1001 = $this->getElementParCle($index, 'A1001');
        $romeA1002 = $this->getElementParCle($index, 'A1002');
        $appellation10 = $this->getElementParCle($romeA1001, '10');
        $appellation20 = $this->getElementParCle($romeA1002, '20');

        $this->assertSame('Appellation fiche', $appellation10['libelle']);
        $this->assertSame('Court référentiel', $appellation10['libelle_court']);
        self::assertTrue($appellation10['peu_utiliser']);
        $this->assertSame('Court fiche', $appellation20['libelle_court']);
        self::assertFalse($appellation20['peu_utiliser']);
    }
}
