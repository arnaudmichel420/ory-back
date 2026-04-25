<?php

declare(strict_types=1);

namespace App\Dto\Metier;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class MetierSavedListQueryDto
{
    public function __construct(
        #[Assert\Positive(message: 'La page doit etre superieure a 0.')]
        public int $page = 1,
        #[Assert\Positive(message: 'Le nombre d\'elements doit etre superieur a 0.')]
        #[Assert\LessThanOrEqual(
            value: 100,
            message: 'Le nombre d\'elements ne doit pas depasser 100.',
        )]
        public int $limit = 20,
    ) {
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}
