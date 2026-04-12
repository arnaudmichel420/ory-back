<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\PoleEmploi;

use App\Service\PoleEmploi\PoleEmploiSourceLoaderService;

final class PoleEmploiSourceLoaderServiceTest extends PoleEmploiServiceTestCase
{
    private string $dossierDataTemporaire;

    protected function setUp(): void
    {
        $this->dossierDataTemporaire = sys_get_temp_dir().'/pole-emploi-test-'.bin2hex(random_bytes(8));

        if (!mkdir($concurrentDirectory = $this->dossierDataTemporaire, 0777, true) && !is_dir($concurrentDirectory)) {
            self::fail(\sprintf('Impossible de creer le dossier temporaire %s.', $this->dossierDataTemporaire));
        }

        $this->creerJeuDeDonneesMinimal();
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dossierDataTemporaire.'/*') ?: [] as $fichier) {
            if (is_file($fichier)) {
                unlink($fichier);
            }
        }

        if (is_dir($this->dossierDataTemporaire)) {
            rmdir($this->dossierDataTemporaire);
        }
    }

    public function testChargeLesSourcesAttenduesDepuisLeDossierData(): void
    {
        $loader = new PoleEmploiSourceLoaderService($this->dossierDataTemporaire);

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

    private function creerJeuDeDonneesMinimal(): void
    {
        $this->ecrireJson('unix_arborescence_principale_v460.json', [
            'arbo_principale' => [
                ['code_domaine' => 'A', 'libelle' => 'Agriculture'],
            ],
        ]);
        $this->ecrireJson('unix_arborescence_centre_interet_v460.json', [
            'arbo_centre_interet' => [
                ['libelle' => 'Nature'],
            ],
        ]);
        $this->ecrireJson('unix_arborescence_secteur_activite_v460.json', [
            'arbo_secteur' => [
                ['code_secteur' => '10', 'libelle' => 'Secteur 10'],
            ],
        ]);
        $this->ecrireJson('unix_referentiel_code_rome_v460.json', [
            ['code_rome' => 'A1001', 'libelle' => 'Metier test'],
        ]);
        $this->ecrireJson('unix_referentiel_appellation_v460.json', [
            ['code_ogr' => '10', 'libelle' => 'Appellation test'],
        ]);
        $this->ecrireJson('unix_referentiel_contexte_travail_v460.json', [
            ['code_ogr' => '10', 'libelle' => 'Contexte test'],
        ]);
        $this->ecrireJson('unix_referentiel_competence_v460.json', [
            'item_referentiel_competence' => [
                ['code_ogr' => '10', 'libelle' => 'Competence test'],
            ],
        ]);
        $this->ecrireJson('unix_referentiel_savoir_v460.json', [
            ['code_ogr' => '20', 'libelle' => 'Savoir test'],
        ]);
        $this->ecrireJson('unix_fiche_emploi_metier_v460.json', [
            ['code_rome' => 'A1001', 'libelle' => 'Fiche test'],
        ]);
    }

    /**
     * @param array<string|int, mixed> $contenu
     */
    private function ecrireJson(string $nomFichier, array $contenu): void
    {
        file_put_contents(
            $this->dossierDataTemporaire.'/'.$nomFichier,
            (string) json_encode($contenu, \JSON_THROW_ON_ERROR)
        );
    }
}
