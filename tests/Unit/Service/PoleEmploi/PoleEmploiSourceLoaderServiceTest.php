<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\PoleEmploi;

use App\Service\PoleEmploi\PoleEmploiSourceLoaderService;

final class PoleEmploiSourceLoaderServiceTest extends PoleEmploiServiceTestCase
{
    public function testChargeLesSourcesAttenduesDepuisLeDossierData(): void
    {
        ini_set('memory_limit', '1024M');

        $loader = new PoleEmploiSourceLoaderService();

        $sources = $loader->charger();

        self::assertArrayHasKey('arbo_principale', $sources);
        self::assertArrayHasKey('arbo_centre_interet', $sources);
        self::assertArrayHasKey('arbo_secteur', $sources);
        self::assertArrayHasKey('referentiel_code_rome', $sources);
        self::assertArrayHasKey('referentiel_appellation', $sources);
        self::assertArrayHasKey('referentiel_contexte', $sources);
        self::assertArrayHasKey('referentiel_competence', $sources);
        self::assertArrayHasKey('referentiel_savoir', $sources);
        self::assertArrayHasKey('fiches_metier', $sources);
        self::assertNotEmpty($sources['referentiel_savoir']);
        self::assertNotEmpty($sources['fiches_metier']);
    }
}
