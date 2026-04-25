<?php

declare(strict_types=1);

namespace App\Dto\Metier;

use App\Enum\EtudiantMetierInteractionTypeEnum;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class MetierInteractionToggleDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Le type d\'interaction est obligatoire.')]
        #[Assert\Choice(
            callback: [EtudiantMetierInteractionTypeEnum::class, 'values'],
            message: 'Le type d\'interaction est invalide.',
        )]
        public ?string $type = null,
    ) {
    }

    public function getTypeEnum(): EtudiantMetierInteractionTypeEnum
    {
        return EtudiantMetierInteractionTypeEnum::from((string) $this->type);
    }
}
