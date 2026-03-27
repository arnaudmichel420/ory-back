<?php

namespace App\Enum;

enum DefiTypeEnum: string
{
    case ACTION = 'action';
    case QUIZ = 'quiz';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
