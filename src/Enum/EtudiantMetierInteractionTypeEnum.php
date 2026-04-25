<?php

declare(strict_types=1);

namespace App\Enum;

enum EtudiantMetierInteractionTypeEnum: string
{
    case VUE = 'vue';
    case SAUVEGARDE = 'sauvegarde';
    case CHALLENGE = 'challenge';

    public function getPoids(): int
    {
        return match ($this) {
            self::VUE => 1,
            self::SAUVEGARDE => 3,
            self::CHALLENGE => 6,
        };
    }

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
