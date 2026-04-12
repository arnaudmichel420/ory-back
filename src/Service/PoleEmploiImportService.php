<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\PoleEmploi\AppellationImportService;
use App\Service\PoleEmploi\CentreInteretImportService;
use App\Service\PoleEmploi\CompetenceImportService;
use App\Service\PoleEmploi\ContexteTravailImportService;
use App\Service\PoleEmploi\DomaineSousDomaineImportService;
use App\Service\PoleEmploi\ImportContext;
use App\Service\PoleEmploi\MetierCentreInteretImportService;
use App\Service\PoleEmploi\MetierCompetenceImportService;
use App\Service\PoleEmploi\MetierContexteTravailImportService;
use App\Service\PoleEmploi\MetierImportService;
use App\Service\PoleEmploi\MetierSecteurImportService;
use App\Service\PoleEmploi\MobiliteImportService;
use App\Service\PoleEmploi\PoleEmploiImportUtils;
use App\Service\PoleEmploi\PoleEmploiSourceLoaderService;
use App\Service\PoleEmploi\SecteurImportService;
use Doctrine\ORM\EntityManagerInterface;

final class PoleEmploiImportService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PoleEmploiSourceLoaderService $sourceLoader,
        private readonly PoleEmploiImportUtils $utils,
        private readonly DomaineSousDomaineImportService $domaineSousDomaineImportService,
        private readonly CentreInteretImportService $centreInteretImportService,
        private readonly SecteurImportService $secteurImportService,
        private readonly ContexteTravailImportService $contexteTravailImportService,
        private readonly CompetenceImportService $competenceImportService,
        private readonly MetierImportService $metierImportService,
        private readonly AppellationImportService $appellationImportService,
        private readonly MetierCompetenceImportService $metierCompetenceImportService,
        private readonly MetierContexteTravailImportService $metierContexteTravailImportService,
        private readonly MobiliteImportService $mobiliteImportService,
        private readonly MetierSecteurImportService $metierSecteurImportService,
        private readonly MetierCentreInteretImportService $metierCentreInteretImportService,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function importer(): array
    {
        \ini_set('memory_limit', '4096M');

        $sources = $this->sourceLoader->charger();
        $contexte = new ImportContext();
        $resume = [
            'referentiels' => [
                'domaines' => $this->utils->nouveauCompteurReferentiel(),
                'sous_domaines' => $this->utils->nouveauCompteurReferentiel(),
                'centres_interet' => $this->utils->nouveauCompteurReferentiel(),
                'secteurs' => $this->utils->nouveauCompteurReferentiel(),
                'contextes_travail' => $this->utils->nouveauCompteurReferentiel(),
                'competences' => $this->utils->nouveauCompteurReferentiel(),
            ],
            'metiers' => $this->utils->nouveauCompteurMetier(),
            'ponts' => [
                'appellations' => $this->utils->nouveauCompteurPont(),
                'metier_competences' => $this->utils->nouveauCompteurPont(),
                'metier_contextes_travail' => $this->utils->nouveauCompteurPont(),
                'mobilites' => $this->utils->nouveauCompteurPont(),
                'metier_secteurs' => $this->utils->nouveauCompteurPont(),
                'metier_centres_interet' => $this->utils->nouveauCompteurPont(),
            ],
        ];

        $contexte->fichesParRome = $this->utils->indexerFichesParCodeRome($sources['fiches_metier']);

        $this->domaineSousDomaineImportService->importer($sources, $contexte, $resume['referentiels']['domaines'], $resume['referentiels']['sous_domaines']);
        $this->centreInteretImportService->importer($sources, $contexte, $resume['referentiels']['centres_interet']);
        $this->secteurImportService->importer($sources, $contexte, $resume['referentiels']['secteurs']);
        $this->contexteTravailImportService->importer($sources, $contexte, $resume['referentiels']['contextes_travail']);
        $this->competenceImportService->importer($sources, $contexte, $resume['referentiels']['competences']);

        $this->metierImportService->importer($sources, $contexte, $resume['metiers']);

        $this->appellationImportService->importer($sources, $contexte, $resume['ponts']['appellations']);
        $this->metierCompetenceImportService->importer($sources, $contexte, $resume['ponts']['metier_competences']);
        $this->metierContexteTravailImportService->importer($sources, $contexte, $resume['ponts']['metier_contextes_travail']);
        $this->mobiliteImportService->importer($sources, $contexte, $resume['ponts']['mobilites']);
        $this->metierSecteurImportService->importer($sources, $contexte, $resume['ponts']['metier_secteurs']);
        $this->metierCentreInteretImportService->importer($sources, $contexte, $resume['ponts']['metier_centres_interet']);

        $this->entityManager->flush();

        return $resume;
    }
}
