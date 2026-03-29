<?php

declare(strict_types=1);

namespace App\Enum;

enum QuestionRecoTypeEnum: string
{
    case SINGLE = 'single';
    case MULTI = 'multi';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
