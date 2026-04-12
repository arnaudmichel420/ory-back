<?php

declare(strict_types=1);

namespace App\Tests\Unit\Command;

use App\Command\ImportMetierAttractiviteCommand;
use App\Entity\MetierAttractiviteImportRun;
use App\Message\ImportMetierAttractiviteMessage;
use App\Repository\MetierRepository;
use App\Repository\TerritoireRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ImportMetierAttractiviteCommandTest extends TestCase
{
    public function testDispatchParBatchEtInitialiseLeRun(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $messageBus = $this->createMock(MessageBusInterface::class);
        $metierRepository = $this->createMock(MetierRepository::class);
        $territoireRepository = $this->createMock(TerritoireRepository::class);

        $metierRepository
            ->expects($this->once())
            ->method('findAttractiviteImportCandidates')
            ->willReturn([
                ['codeOgr' => 'M1', 'codeRome' => 'R1'],
                ['codeOgr' => 'M2', 'codeRome' => 'R2'],
                ['codeOgr' => 'M3', 'codeRome' => 'R3'],
            ]);

        $territoireRepository
            ->expects($this->once())
            ->method('findCodesByType')
            ->willReturn(['75', '92']);

        $persistedRun = null;

        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(MetierAttractiviteImportRun::class))
            ->willReturnCallback(static function (MetierAttractiviteImportRun $run) use (&$persistedRun): void {
                $persistedRun = $run;
            });

        $entityManager
            ->expects($this->once())
            ->method('flush')
            ->willReturnCallback(static function () use (&$persistedRun): void {
                self::assertInstanceOf(MetierAttractiviteImportRun::class, $persistedRun);

                $reflection = new \ReflectionProperty(MetierAttractiviteImportRun::class, 'id');
                $reflection->setValue($persistedRun, 42);
            });

        $dispatchedMessages = [];

        $messageBus
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->with($this->isInstanceOf(ImportMetierAttractiviteMessage::class))
            ->willReturnCallback(static function (ImportMetierAttractiviteMessage $message) use (&$dispatchedMessages): Envelope {
                $dispatchedMessages[] = $message;

                return new Envelope($message);
            });

        $command = new ImportMetierAttractiviteCommand(
            $entityManager,
            $messageBus,
            $metierRepository,
            $territoireRepository,
        );

        $tester = new CommandTester($command);
        $exitCode = $tester->execute([
            '--batch-size' => 4,
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertInstanceOf(MetierAttractiviteImportRun::class, $persistedRun);
        /* @var MetierAttractiviteImportRun $persistedRun */

        $this->assertSame(6, $persistedRun->getTotalPairs());
        $this->assertSame(2, $persistedRun->getTotalBatches());
        $this->assertCount(2, $dispatchedMessages);
        $this->assertCount(4, $dispatchedMessages[0]->getPairs());
        $this->assertCount(2, $dispatchedMessages[1]->getPairs());
        $this->assertSame(42, $dispatchedMessages[0]->getRunId());
        $this->assertStringContainsString('Couples dispatches : 6', $tester->getDisplay());
    }
}
