<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class EtudiantPasswordDto
{
    private const PASSWORD_REGEX = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).+$/';

    public function __construct(
        #[Assert\NotBlank(message: 'Le mot de passe actuel est obligatoire.')]
        #[Assert\Length(max: 255, maxMessage: 'Le mot de passe actuel ne doit pas depasser 255 caracteres.')]
        public ?string $currentPassword = null,
        #[Assert\NotBlank(message: 'Le nouveau mot de passe est obligatoire.')]
        #[Assert\Length(
            min: 8,
            max: 255,
            minMessage: 'Le nouveau mot de passe doit contenir au moins 8 caracteres.',
            maxMessage: 'Le nouveau mot de passe ne doit pas depasser 255 caracteres.',
        )]
        #[Assert\Regex(
            pattern: self::PASSWORD_REGEX,
            message: 'Le nouveau mot de passe doit contenir une minuscule, une majuscule, un chiffre et un caractere special.',
        )]
        public ?string $newPassword = null,
    ) {
    }
}
