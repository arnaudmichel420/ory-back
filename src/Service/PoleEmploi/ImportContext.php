<?php

declare(strict_types=1);

namespace App\Service\PoleEmploi;

final class ImportContext
{
    /** @var array<string, \App\Entity\Domaine> */
    public array $domainesParCode = [];

    /** @var array<string, \App\Entity\SousDomaine> */
    public array $sousDomainesParCode = [];

    /** @var array<string, string> */
    public array $codeSousDomaineParRome = [];

    /** @var array<string, \App\Entity\CentreInteret> */
    public array $centresInteretParCle = [];

    /** @var array<string, array<string, array{centre_interet: \App\Entity\CentreInteret, principal: ?bool}>> */
    public array $liaisonsCentreInteretParRome = [];

    /** @var array<string, \App\Entity\Secteur> */
    public array $secteursParCode = [];

    /** @var array<string, \App\Entity\ContexteTravail> */
    public array $contextesTravailParCode = [];

    /** @var array<string, \App\Entity\Competence> */
    public array $competencesParCode = [];

    /** @var array<string, \App\Entity\Metier> */
    public array $metiersParCodeRome = [];

    /** @var array<string, array<string, mixed>> */
    public array $fichesParRome = [];
}
