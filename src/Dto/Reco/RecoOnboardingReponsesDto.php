<?php

declare(strict_types=1);

namespace App\Dto\Reco;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class RecoOnboardingReponsesDto
{
    /**
     * @param list<int>|null $choixIds
     */
    public function __construct(
        #[Assert\NotBlank(message: 'Les choix sont obligatoires.')]
        #[Assert\Type(type: 'array', message: 'Les choix doivent etre envoyes sous forme de liste.')]
        #[Assert\Count(min: 1, minMessage: 'Au moins un choix est obligatoire.')]
        #[Assert\All([
            new Assert\Type(type: 'integer', message: 'Chaque choix doit etre un identifiant entier.'),
            new Assert\Positive(message: 'Chaque choix doit etre un identifiant positif.'),
        ])]
        public ?array $choixIds = null,
    ) {
    }

    /**
     * @return list<int>
     */
    public function getChoixIds(): array
    {
        if (null === $this->choixIds) {
            return [];
        }

        return array_values(array_unique($this->choixIds));
    }
}
