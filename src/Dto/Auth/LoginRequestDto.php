<?php

declare(strict_types=1);

namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class LoginRequestDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'L\'email est obligatoire.')]
        #[Assert\Email(message: 'L\'email est invalide.')]
        #[Assert\Length(
            max: 180,
            maxMessage: 'L\'email ne doit pas depasser 180 caracteres.',
        )]
        public ?string $email = null,
        #[Assert\NotBlank(message: 'Le mot de passe est obligatoire.')]
        #[Assert\Length(
            max: 255,
            maxMessage: 'Le mot de passe ne doit pas depasser 255 caracteres.',
        )]
        public ?string $password = null,
    ) {
    }
}
