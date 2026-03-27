<?php

namespace App\Enum;

enum QuestionQuizDefiTypeEnum: string
{
    case VRAI_FAUX = 'vrai_faux';
    case MULTIPLE = 'multiple';
    case SIMPLE = 'simple';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
