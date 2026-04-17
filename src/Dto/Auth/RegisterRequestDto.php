<?php

declare(strict_types=1);

namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class RegisterRequestDto
{
    private const PASSWORD_REGEX = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).+$/';

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
            min: 8,
            max: 255,
            minMessage: 'Le mot de passe doit contenir au moins 8 caracteres.',
            maxMessage: 'Le mot de passe ne doit pas depasser 255 caracteres.',
        )]
        #[Assert\Regex(
            pattern: self::PASSWORD_REGEX,
            message: 'Le mot de passe doit contenir une minuscule, une majuscule, un chiffre et un caractere special.',
        )]
        public ?string $password = null,
    ) {
    }
}
