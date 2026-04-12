<?php

declare(strict_types=1);

namespace App\Message;

final readonly class ImportMetierAttractiviteMessage
{
    /**
     * @param list<array{codeRome:string,codeOgrMetier:string,codeDepartement:string}> $pairs
     */
    public function __construct(
        private int $runId,
        private array $pairs,
    ) {
    }

    public function getRunId(): int
    {
        return $this->runId;
    }

    /**
     * @return list<array{codeRome:string,codeOgrMetier:string,codeDepartement:string}>
     */
    public function getPairs(): array
    {
        return $this->pairs;
    }
}
