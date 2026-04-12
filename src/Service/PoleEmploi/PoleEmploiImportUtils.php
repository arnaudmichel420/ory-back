<?php

declare(strict_types=1);

namespace App\Service\PoleEmploi;

use App\Enum\MetierCompetenceTypeEnum;

final class PoleEmploiImportUtils
{
    /**
     * @param list<array<string, mixed>> $fiches
     *
     * @return array<string, array<string, mixed>>
     */
    public function indexerFichesParCodeRome(array $fiches): array
    {
        $index = [];

        foreach ($fiches as $fiche) {
            $codeRome = $this->normaliserCode($fiche['rome']['code_rome'] ?? null);
            if (null !== $codeRome) {
                $index[$codeRome] = $fiche;
            }
        }

        return $index;
    }

    /**
     * @param list<array<string, mixed>> $referentielAppellation
     * @param array<string, array<string, mixed>> $fichesParRome
     *
     * @return array<string, array<string, array<string, mixed>>>
     */
    public function indexerAppellationsParCodeRome(array $referentielAppellation, array $fichesParRome): array
    {
        $index = [];
        $referentielParCodeOgr = [];

        foreach ($referentielAppellation as $ligneAppellation) {
            $codeOgr = $this->normaliserCode($ligneAppellation['code_ogr'] ?? null);
            $libelle = $this->normaliserTexte($ligneAppellation['libelle'] ?? null);

            if (null === $codeOgr || null === $libelle) {
                continue;
            }

            $referentielParCodeOgr[$codeOgr] = [
                'code_ogr' => $codeOgr,
                'libelle' => $libelle,
                'libelle_court' => $this->normaliserTexte($ligneAppellation['libelle_court'] ?? null),
                'peu_utiliser' => $this->normaliserOuiNon($ligneAppellation['peu_usite'] ?? null),
            ];
        }

        foreach ($fichesParRome as $codeRome => $fiche) {
            foreach (($fiche['appellations'] ?? []) as $ligneAppellation) {
                $codeOgr = $this->normaliserCode($ligneAppellation['code_ogr'] ?? null);
                $libelle = $this->normaliserTexte($ligneAppellation['libelle'] ?? null);

                if (null === $codeOgr || null === $libelle) {
                    continue;
                }

                $index[$codeRome][$codeOgr] = [
                    'code_ogr' => $codeOgr,
                    'libelle' => $libelle,
                    'libelle_court' => $this->normaliserTexte($ligneAppellation['libelle_court'] ?? null)
                        ?? ($referentielParCodeOgr[$codeOgr]['libelle_court'] ?? null),
                    'peu_utiliser' => $referentielParCodeOgr[$codeOgr]['peu_utiliser'] ?? null,
                ];
            }
        }

        return $index;
    }

    public function determinerTypeCompetence(mixed $categorie): ?MetierCompetenceTypeEnum
    {
        $categorieNormalisee = $this->normaliserCleTexte(\is_scalar($categorie) ? (string) $categorie : null);

        return match ($categorieNormalisee) {
            'savoir-faire' => MetierCompetenceTypeEnum::SAVOIR_FAIRE,
            'savoir-tre-professionnel', 'savoir-etre-professionnel' => MetierCompetenceTypeEnum::SAVOIR_ETRE_PROFESSIONEL,
            'produits-outils-et-matieres',
            'produits-outils-et-matires',
            'produits-outils-et-mati-eres',
            'techniques-professionnelles',
            'domaines-dexpertise',
            'domaines-d-expertise',
            'normes-et-procedes',
            'normes-et-procds',
            'normes-et-proc-ed-es',
            'certifications-et-habilitations' => MetierCompetenceTypeEnum::SAVOIR,
            default => null,
        };
    }

    public function normaliserTransitionEco(mixed $valeur): ?bool
    {
        if (null === $valeur) {
            return null;
        }

        $normalisee = $this->normaliserCleTexte(\is_scalar($valeur) ? (string) $valeur : null);
        if (null === $normalisee) {
            return null;
        }

        if (\str_contains($normalisee, 'transition-ecologique') || \str_contains($normalisee, 'emploi-vert')) {
            return true;
        }

        if (\str_contains($normalisee, 'emploi-blanc') || \str_contains($normalisee, 'emploi-brun')) {
            return false;
        }

        return null;
    }

    public function normaliserOuiNon(mixed $valeur): ?bool
    {
        if (\is_bool($valeur)) {
            return $valeur;
        }

        $normalisee = $this->normaliserCleTexte(\is_scalar($valeur) ? (string) $valeur : null);

        return match ($normalisee) {
            'o', 'oui', 'true', '1' => true,
            'n', 'non', 'false', '0' => false,
            default => null,
        };
    }

    public function normaliserBooleen(mixed $valeur): ?bool
    {
        if (\is_bool($valeur)) {
            return $valeur;
        }

        if (\is_int($valeur)) {
            return 1 === $valeur;
        }

        return $this->normaliserOuiNon($valeur);
    }

    public function normaliserEntier(mixed $valeur): ?int
    {
        if (\is_int($valeur)) {
            return $valeur;
        }

        if (\is_string($valeur) && '' !== \trim($valeur) && \is_numeric($valeur)) {
            return (int) $valeur;
        }

        return null;
    }

    public function normaliserCoeurMetier(mixed $valeur): int
    {
        $normalisee = $this->normaliserCleTexte(\is_scalar($valeur) ? (string) $valeur : null);

        return match ($normalisee) {
            'principale' => 2,
            'emergente' => 1,
            default => 0,
        };
    }

    public function extraireCodeRomeCible(mixed $valeur): ?string
    {
        if (!\is_string($valeur)) {
            return null;
        }

        $parties = \explode(' - ', $valeur, 2);

        return $this->normaliserCode($parties[0]);
    }

    public function normaliserCode(mixed $valeur): ?string
    {
        if (null === $valeur || !\is_scalar($valeur)) {
            return null;
        }

        $code = \trim((string) $valeur);

        return '' === $code ? null : $code;
    }

    public function normaliserTexte(mixed $valeur): ?string
    {
        if (null === $valeur || !\is_scalar($valeur)) {
            return null;
        }

        $texte = \trim((string) $valeur);
        if ('' === $texte) {
            return null;
        }

        $texte = (string) \preg_replace('/\s+/u', ' ', $texte);

        return '' === $texte ? null : $texte;
    }

    public function normaliserCleTexte(?string $texte): ?string
    {
        $texte = $this->normaliserTexte($texte);
        if (null === $texte) {
            return null;
        }

        $ascii = \iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texte);
        if (false !== $ascii) {
            $texte = $ascii;
        }

        $texte = \strtolower($texte);
        $texte = (string) \preg_replace('/[^a-z0-9]+/', '-', $texte);
        $texte = \trim($texte, '-');

        return '' === $texte ? null : $texte;
    }

    /**
     * @return array{created: int, updated: int, ignored: int}
     */
    public function nouveauCompteurReferentiel(): array
    {
        return ['created' => 0, 'updated' => 0, 'ignored' => 0];
    }

    /**
     * @return array{created: int, updated: int, ignored: int}
     */
    public function nouveauCompteurMetier(): array
    {
        return ['created' => 0, 'updated' => 0, 'ignored' => 0];
    }

    /**
     * @return array{created: int, updated: int, deleted: int, ignored: int}
     */
    public function nouveauCompteurPont(): array
    {
        return ['created' => 0, 'updated' => 0, 'deleted' => 0, 'ignored' => 0];
    }
}
