<?php

namespace App\Enum;

enum EtudiantDefiStatutEnum: string
{
    case EN_COURS = 'en_cours';
    case TERMINE = 'termine';
    case ABANDONNE = 'abandonne';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
