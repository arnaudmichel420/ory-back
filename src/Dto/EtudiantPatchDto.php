<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class EtudiantPatchDto
{
    public function __construct(
        #[Assert\Length(max: 255, maxMessage: 'Le nom ne doit pas depasser 255 caracteres.')]
        public ?string $nom = null,
        #[Assert\Length(max: 255, maxMessage: 'Le prenom ne doit pas depasser 255 caracteres.')]
        public ?string $prenom = null,
        public ?string $adresse = null,
        #[Assert\Length(max: 255, maxMessage: 'La ville ne doit pas depasser 255 caracteres.')]
        public ?string $ville = null,
        #[Assert\Length(max: 10, maxMessage: 'Le code postal ne doit pas depasser 10 caracteres.')]
        public ?string $codePostal = null,
        #[Assert\Length(max: 20, maxMessage: 'Le telephone ne doit pas depasser 20 caracteres.')]
        public ?string $telephone = null,
    ) {
    }
}
