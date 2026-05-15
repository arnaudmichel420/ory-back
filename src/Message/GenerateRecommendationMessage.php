<?php

declare(strict_types=1);

namespace App\Message;

final readonly class GenerateRecommendationMessage
{
    public function __construct(
        private int $etudiantId,
    ) {
    }

    public function getEtudiantId(): int
    {
        return $this->etudiantId;
    }
}
