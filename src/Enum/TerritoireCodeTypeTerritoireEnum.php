<?php

declare(strict_types=1);

namespace App\Enum;

enum TerritoireCodeTypeTerritoireEnum: string
{
    case DEP = 'DEP';
    case REG = 'REG';
    case NAT = 'NAT';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
