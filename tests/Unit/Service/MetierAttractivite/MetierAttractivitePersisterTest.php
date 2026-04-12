<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\MetierAttractivite;

use App\Entity\MetierAttractivite;
use App\Entity\Territoire;
use App\Enum\MetierAttractiviteCodeEnum;
use App\Enum\TerritoireCodeTypeTerritoireEnum;
use App\Repository\MetierAttractiviteRepository;
use App\Service\MetierAttractivite\MetierAttractivitePersister;
use App\Tests\Unit\Service\PoleEmploi\PoleEmploiServiceTestCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

final class MetierAttractivitePersisterTest extends PoleEmploiServiceTestCase
{
    private EntityManagerInterface&MockObject $entityManager;
    private MetierAttractiviteRepository&MockObject $repository;
    private MetierAttractivitePersister $persister;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(MetierAttractiviteRepository::class);
        $this->persister = new MetierAttractivitePersister($this->entityManager, $this->repository);
    }

    public function testCreeMetAJourEtSupprimeSelonLeSnapshot(): void
    {
        $metier = $this->createMetier('100', 'A1203');
        $territoire = (new Territoire())
            ->setCodeTypeTerritoire(TerritoireCodeTypeTerritoireEnum::DEP)
            ->setCodeTerritoire('75')
            ->setLibelleTerritoire('Paris');

        $existingUpdated = (new MetierAttractivite())
            ->setCodeOgrMetier($metier)
            ->setTerritoire($territoire)
            ->setCodeAttractivite(MetierAttractiviteCodeEnum::INT_EMB)
            ->setValeur(1);
        $existingDeleted = (new MetierAttractivite())
            ->setCodeOgrMetier($metier)
            ->setTerritoire($territoire)
            ->setCodeAttractivite(MetierAttractiviteCodeEnum::PERSPECTIVE)
            ->setValeur(5);

        $this->repository
            ->expects($this->once())
            ->method('findByMetierAndTerritoire')
            ->with($metier, $territoire)
            ->willReturn([$existingUpdated, $existingDeleted]);

        $persisted = [];
        $removed = [];

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->willReturnCallback(static function (object $entity) use (&$persisted): void {
                $persisted[] = $entity;
            });

        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($existingDeleted)
            ->willReturnCallback(static function (object $entity) use (&$removed): void {
                $removed[] = $entity;
            });

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $stats = $this->persister->persistSnapshot($metier, $territoire, [
            'INT_EMB' => 4,
            'COND_TRAVAIL' => 2,
        ]);

        $this->assertSame([
            'created' => 1,
            'updated' => 1,
            'deleted' => 1,
        ], $stats);

        $this->assertSame(4, $existingUpdated->getValeur());
        self::assertCount(1, $persisted);
        $this->assertSame(MetierAttractiviteCodeEnum::COND_TRAVAIL, $persisted[0]->getCodeAttractivite());
        $this->assertSame(2, $persisted[0]->getValeur());
        $this->assertSame([$existingDeleted], $removed);
    }
}
