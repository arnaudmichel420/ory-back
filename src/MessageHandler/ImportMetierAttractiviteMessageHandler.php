<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\ImportMetierAttractiviteMessage;
use App\Service\MetierAttractivite\MetierAttractiviteImportService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ImportMetierAttractiviteMessageHandler
{
    public function __construct(
        private MetierAttractiviteImportService $importService,
    ) {
    }

    public function __invoke(ImportMetierAttractiviteMessage $message): void
    {
        $this->importService->importBatch($message->getRunId(), $message->getPairs());
    }
}
