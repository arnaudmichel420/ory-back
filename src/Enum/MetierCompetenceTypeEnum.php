<?php

declare(strict_types=1);

namespace App\Enum;

enum MetierCompetenceTypeEnum: string
{
    case SAVOIR_FAIRE = 'savoir_faire';
    case SAVOIR_ETRE_PROFESSIONEL = 'savoir_etre_professionel';
    case SAVOIR = 'savoir';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
