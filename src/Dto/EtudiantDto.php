<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class EtudiantDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
        #[Assert\Length(max: 255, maxMessage: 'Le nom ne doit pas depasser 255 caracteres.')]
        public ?string $nom = null,
        #[Assert\NotBlank(message: 'Le prenom est obligatoire.')]
        #[Assert\Length(max: 255, maxMessage: 'Le prenom ne doit pas depasser 255 caracteres.')]
        public ?string $prenom = null,
        #[Assert\NotBlank(message: 'L\'adresse est obligatoire.')]
        public ?string $adresse = null,
        #[Assert\NotBlank(message: 'La ville est obligatoire.')]
        #[Assert\Length(max: 255, maxMessage: 'La ville ne doit pas depasser 255 caracteres.')]
        public ?string $ville = null,
        #[Assert\NotBlank(message: 'Le code postal est obligatoire.')]
        #[Assert\Length(max: 10, maxMessage: 'Le code postal ne doit pas depasser 10 caracteres.')]
        public ?string $codePostal = null,
        #[Assert\NotBlank(message: 'Le telephone est obligatoire.')]
        #[Assert\Length(max: 20, maxMessage: 'Le telephone ne doit pas depasser 20 caracteres.')]
        public ?string $telephone = null,
    ) {
    }
}
