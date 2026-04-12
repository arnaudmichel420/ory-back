<?php

declare(strict_types=1);

namespace App\Enum;

enum MetierAttractiviteCodeEnum: string
{
    case ATTR_SALARIALE = 'ATTR_SALARIALE';
    case DUR_EMPL = 'DUR_EMPL';
    case INT_EMB = 'INT_EMB';
    case SPECIF_FORM_EMPL = 'SPECIF_FORM_EMPL';
    case MISMATCH_GEO = 'MISMATCH_GEO';
    case MAIN_OEUVRE = 'MAIN_OEUVRE';
    case PERSPECTIVE = 'PERSPECTIVE';
    case COND_TRAVAIL = 'COND_TRAVAIL';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::ATTR_SALARIALE => 'Attractivite salariale',
            self::DUR_EMPL => 'Durabilite de l\'emploi',
            self::INT_EMB => 'Intensite d\'embauche',
            self::SPECIF_FORM_EMPL => 'Lien formation - metier',
            self::MISMATCH_GEO => 'Inadequation geographique',
            self::MAIN_OEUVRE => 'Manque de main d\'oeuvre',
            self::PERSPECTIVE => 'Indicateur principal tension',
            self::COND_TRAVAIL => 'Conditions de travail',
        };
    }
}
