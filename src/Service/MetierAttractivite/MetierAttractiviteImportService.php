<?php

declare(strict_types=1);

namespace App\Service\MetierAttractivite;

use App\Entity\MetierAttractiviteImportRun;
use App\Repository\MetierAttractiviteImportRunRepository;
use App\Repository\MetierRepository;
use App\Repository\TerritoireRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class MetierAttractiviteImportService
{
    private const MAX_RETRIES = 5;
    private const BASE_BACKOFF_MILLISECONDS = 500;
    private const MAX_BACKOFF_MILLISECONDS = 10000;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private MetierRepository $metierRepository,
        private TerritoireRepository $territoireRepository,
        private MetierAttractiviteImportRunRepository $runRepository,
        private MetierAttractiviteApiService $apiService,
        private MetierAttractivitePersister $persister,
    ) {
    }

    /**
     * @param list<array{codeRome:string,codeOgrMetier:string,codeDepartement:string}> $pairs
     */
    public function importBatch(int $runId, array $pairs): void
    {
        $run = $this->runRepository->find($runId);
        if (!$run instanceof MetierAttractiviteImportRun) {
            throw new \RuntimeException(\sprintf('Run d\'import attractivite %d introuvable.', $runId));
        }

        if (MetierAttractiviteImportRun::STATUS_PENDING === $run->getStatus()) {
            $run->setStatus(MetierAttractiviteImportRun::STATUS_RUNNING);
        }

        foreach ($pairs as $pair) {
            $metier = $this->metierRepository->find($pair['codeOgrMetier']);
            $territoire = $this->territoireRepository->findOneByTypeAndCode(
                \App\Enum\TerritoireCodeTypeTerritoireEnum::DEP,
                $pair['codeDepartement'],
            );

            if (null === $metier || null === $territoire) {
                $run
                    ->incrementProcessedPairs()
                    ->incrementErrorPairs();
                continue;
            }

            try {
                $snapshot = $this->fetchWithRetry($pair['codeDepartement'], $pair['codeRome']);
                $stats = $this->persister->persistSnapshot($metier, $territoire, $snapshot['values']);

                $run
                    ->incrementProcessedPairs()
                    ->incrementIgnoredValues($snapshot['ignored'])
                    ->incrementCreatedValues($stats['created'])
                    ->incrementUpdatedValues($stats['updated'])
                    ->incrementDeletedValues($stats['deleted']);
            } catch (MetierAttractiviteApiException) {
                $run
                    ->incrementProcessedPairs()
                    ->incrementErrorPairs();
            }

            $this->entityManager->flush();
            $this->entityManager->clear();

            $run = $this->runRepository->find($runId);
            if (!$run instanceof MetierAttractiviteImportRun) {
                throw new \RuntimeException(\sprintf('Run d\'import attractivite %d introuvable apres clear.', $runId));
            }
        }

        $run->incrementProcessedBatches();
        if ($run->getProcessedBatches() >= $run->getTotalBatches()) {
            $run->finalize();
        }

        $this->entityManager->flush();
    }

    /**
     * @return array{values: array<string, int>, ignored: int}
     */
    private function fetchWithRetry(string $codeDepartement, string $codeRome): array
    {
        $attempt = 0;

        while (true) {
            try {
                return $this->apiService->fetchSnapshot($codeDepartement, $codeRome);
            } catch (MetierAttractiviteApiRetryableException $exception) {
                ++$attempt;
                if ($attempt >= self::MAX_RETRIES) {
                    throw $exception;
                }

                $retryAfter = $exception->getRetryAfterMilliseconds();
                $backoff = min(
                    self::MAX_BACKOFF_MILLISECONDS,
                    self::BASE_BACKOFF_MILLISECONDS * (2 ** ($attempt - 1)),
                );
                $jitter = random_int(0, 250);
                $delay = max($retryAfter ?? 0, $backoff + $jitter);
                usleep($delay * 1000);
            }
        }
    }
}
