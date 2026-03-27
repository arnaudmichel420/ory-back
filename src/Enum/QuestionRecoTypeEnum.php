<?php

namespace App\Enum;

enum QuestionRecoTypeEnum: string
{
    case SINGLE = 'single';
    case MULTI = 'multi';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
