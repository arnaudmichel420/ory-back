<?php

declare(strict_types=1);

namespace App\Service\PoleEmploi;

class PoleEmploiSourceLoaderService
{
    private const DOSSIER_DATA = '/data/';
    private const FICHIER_ARBO_PRINCIPALE = 'unix_arborescence_principale_v460.json';
    private const FICHIER_ARBO_CENTRE_INTERET = 'unix_arborescence_centre_interet_v460.json';
    private const FICHIER_ARBO_SECTEUR = 'unix_arborescence_secteur_activite_v460.json';
    private const FICHIER_REFERENTIEL_CODE_ROME = 'unix_referentiel_code_rome_v460.json';
    private const FICHIER_REFERENTIEL_APPELLATION = 'unix_referentiel_appellation_v460.json';
    private const FICHIER_REFERENTIEL_CONTEXTE = 'unix_referentiel_contexte_travail_v460.json';
    private const FICHIER_REFERENTIEL_COMPETENCE = 'unix_referentiel_competence_v460.json';
    private const FICHIER_REFERENTIEL_SAVOIR = 'unix_referentiel_savoir_v460.json';
    private const FICHIER_FICHE_METIER = 'unix_fiche_emploi_metier_v460.json';

    /**
     * @return array<string, mixed>
     */
    public function charger(): array
    {
        $arboPrincipale = $this->chargerJson(self::FICHIER_ARBO_PRINCIPALE);
        $arboCentreInteret = $this->chargerJson(self::FICHIER_ARBO_CENTRE_INTERET);
        $arboSecteur = $this->chargerJson(self::FICHIER_ARBO_SECTEUR);
        $referentielCodeRome = $this->chargerJson(self::FICHIER_REFERENTIEL_CODE_ROME);
        $referentielAppellation = $this->chargerJson(self::FICHIER_REFERENTIEL_APPELLATION);
        $referentielContexte = $this->chargerJson(self::FICHIER_REFERENTIEL_CONTEXTE);
        $referentielCompetence = $this->chargerJson(self::FICHIER_REFERENTIEL_COMPETENCE);
        $referentielSavoir = $this->chargerJson(self::FICHIER_REFERENTIEL_SAVOIR);
        $fichesMetier = $this->chargerJson(self::FICHIER_FICHE_METIER);

        return [
            'arbo_principale' => $arboPrincipale['arbo_principale'] ?? [],
            'arbo_centre_interet' => $arboCentreInteret['arbo_centre_interet'] ?? [],
            'arbo_secteur' => $arboSecteur['arbo_secteur'] ?? [],
            'referentiel_code_rome' => $referentielCodeRome,
            'referentiel_appellation' => $referentielAppellation,
            'referentiel_contexte' => $referentielContexte,
            'referentiel_competence' => $referentielCompetence['item_referentiel_competence'] ?? [],
            'referentiel_savoir' => $referentielSavoir,
            'fiches_metier' => $fichesMetier,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function chargerJson(string $nomFichier): array
    {
        $chemin = \dirname(__DIR__, 3).self::DOSSIER_DATA.$nomFichier;
        $contenu = file_get_contents($chemin);

        if (false === $contenu) {
            throw new \RuntimeException(\sprintf('Impossible de lire le fichier %s.', $chemin));
        }

        $donnees = json_decode($contenu, true);
        if (\JSON_ERROR_NONE === json_last_error() && \is_array($donnees)) {
            return $donnees;
        }

        foreach (['Windows-1252', 'ISO-8859-1'] as $encodage) {
            $converti = iconv($encodage, 'UTF-8//IGNORE', $contenu);
            if (false === $converti) {
                continue;
            }

            $donnees = json_decode($converti, true);
            if (\JSON_ERROR_NONE === json_last_error() && \is_array($donnees)) {
                return $donnees;
            }
        }

        $donnees = json_decode($contenu, true, 512, \JSON_INVALID_UTF8_IGNORE);
        if (\is_array($donnees)) {
            return $donnees;
        }

        throw new \RuntimeException(\sprintf('Le fichier %s ne contient pas un JSON exploitable.', $chemin));
    }
}
