<?php

namespace App\Enum;

enum EtudiantMetierInteractionTypeEnum: string
{
    case VUE = 'vue';
    case SAUVEGARDE = 'sauvegarde';
    case CHALLENGE = 'challenge';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
