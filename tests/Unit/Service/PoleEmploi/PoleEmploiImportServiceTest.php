<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\PoleEmploi;

use App\Service\PoleEmploi\AppellationImportService;
use App\Service\PoleEmploi\CentreInteretImportService;
use App\Service\PoleEmploi\CompetenceImportService;
use App\Service\PoleEmploi\ContexteTravailImportService;
use App\Service\PoleEmploi\DomaineSousDomaineImportService;
use App\Service\PoleEmploi\MetierCentreInteretImportService;
use App\Service\PoleEmploi\MetierCompetenceImportService;
use App\Service\PoleEmploi\MetierContexteTravailImportService;
use App\Service\PoleEmploi\MetierImportService;
use App\Service\PoleEmploi\MetierSecteurImportService;
use App\Service\PoleEmploi\MobiliteImportService;
use App\Service\PoleEmploi\PoleEmploiImportUtils;
use App\Service\PoleEmploi\PoleEmploiSourceLoaderService;
use App\Service\PoleEmploi\SecteurImportService;
use App\Service\PoleEmploiImportService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

final class PoleEmploiImportServiceTest extends PoleEmploiServiceTestCase
{
    public function testOrchestreLesSousServicesDansLeBonOrdreEtRetourneLeResume(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())->method('flush');

        $sourceLoader = $this->createMock(PoleEmploiSourceLoaderService::class);
        $sourceLoader->expects($this->once())->method('charger')->willReturn([
            'fiches_metier' => [
                ['rome' => ['code_rome' => 'A1001']],
            ],
        ]);

        $utils = $this->createMock(PoleEmploiImportUtils::class);
        $utils->method('nouveauCompteurReferentiel')->willReturn(['created' => 0, 'updated' => 0, 'ignored' => 0]);
        $utils->method('nouveauCompteurMetier')->willReturn(['created' => 0, 'updated' => 0, 'ignored' => 0]);
        $utils->method('nouveauCompteurPont')->willReturn(['created' => 0, 'updated' => 0, 'deleted' => 0, 'ignored' => 0]);
        $utils->expects($this->once())->method('indexerFichesParCodeRome')->willReturn(['A1001' => ['rome' => ['code_rome' => 'A1001']]]);

        $domaineService = $this->createServiceMock(DomaineSousDomaineImportService::class);
        $centreInteretService = $this->createServiceMock(CentreInteretImportService::class);
        $secteurService = $this->createServiceMock(SecteurImportService::class);
        $contexteService = $this->createServiceMock(ContexteTravailImportService::class);
        $competenceService = $this->createServiceMock(CompetenceImportService::class);
        $metierService = $this->createServiceMock(MetierImportService::class);
        $appellationService = $this->createServiceMock(AppellationImportService::class);
        $metierCompetenceService = $this->createServiceMock(MetierCompetenceImportService::class);
        $metierContexteService = $this->createServiceMock(MetierContexteTravailImportService::class);
        $mobiliteService = $this->createServiceMock(MobiliteImportService::class);
        $metierSecteurService = $this->createServiceMock(MetierSecteurImportService::class);
        $metierCentreInteretService = $this->createServiceMock(MetierCentreInteretImportService::class);

        foreach ([
            $domaineService,
            $centreInteretService,
            $secteurService,
            $contexteService,
            $competenceService,
            $metierService,
            $appellationService,
            $metierCompetenceService,
            $metierContexteService,
            $mobiliteService,
            $metierSecteurService,
            $metierCentreInteretService,
        ] as $service) {
            $service->expects($this->once())->method('importer');
        }

        $service = new PoleEmploiImportService(
            $entityManager,
            $sourceLoader,
            $utils,
            $domaineService,
            $centreInteretService,
            $secteurService,
            $contexteService,
            $competenceService,
            $metierService,
            $appellationService,
            $metierCompetenceService,
            $metierContexteService,
            $mobiliteService,
            $metierSecteurService,
            $metierCentreInteretService,
        );

        $resume = $service->importer();

        self::assertArrayHasKey('referentiels', $resume);
        self::assertArrayHasKey('metiers', $resume);
        self::assertArrayHasKey('ponts', $resume);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T&MockObject
     */
    private function createServiceMock(string $class): object
    {
        return $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->onlyMethods(['importer'])
            ->getMock();
    }
}
