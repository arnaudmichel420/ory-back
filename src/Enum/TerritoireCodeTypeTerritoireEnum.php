<?php

namespace App\Enum;

enum TerritoireCodeTypeTerritoireEnum: string
{
    case DEP = 'DEP';
    case REG = 'REG';
    case NAT = 'NAT';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
