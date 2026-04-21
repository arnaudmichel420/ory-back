<?php

declare(strict_types=1);

namespace App\Dto\Metier;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class MetierListQueryDto
{
    /**
     * @param list<int|string> $secteurIds
     */
    public function __construct(
        #[Assert\Positive(message: 'La page doit etre superieure a 0.')]
        public int $page = 1,
        #[Assert\Positive(message: 'Le nombre d\'elements par page doit etre superieur a 0.')]
        #[Assert\LessThanOrEqual(
            value: 100,
            message: 'Le nombre d\'elements par page ne doit pas depasser 100.',
        )]
        public int $perPage = 20,
        #[Assert\Choice(
            choices: ['libelle', '-libelle'],
            message: 'Le tri doit etre "libelle" ou "-libelle".',
        )]
        public string $sort = 'libelle',
        #[Assert\Length(
            max: 255,
            maxMessage: 'La recherche ne doit pas depasser 255 caracteres.',
        )]
        public ?string $search = null,
        #[Assert\All([
            new Assert\Regex(
                pattern: '/^\d+$/',
                message: 'Chaque identifiant de secteur doit etre un entier positif.',
            ),
        ])]
        public array $secteurIds = [],
        public ?bool $transitionEco = null,
        public ?bool $transitionNum = null,
        public ?bool $emploiCadre = null,
    ) {
    }

    /**
     * @return list<int>
     */
    public function getSecteurIdsAsInts(): array
    {
        return array_map(
            static fn (int|string $secteurId): int => (int) $secteurId,
            $this->secteurIds,
        );
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }

    public function getSortDirection(): string
    {
        return str_starts_with($this->sort, '-') ? 'DESC' : 'ASC';
    }

    public function getSortField(): string
    {
        return 'libelle';
    }
}
