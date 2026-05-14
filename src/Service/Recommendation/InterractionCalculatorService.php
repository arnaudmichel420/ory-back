<?php

namespace App\Service\Recommendation;

use App\Entity\Etudiant;

final class InterractionCalculatorService
{
    public function getInterractionScoreForStudent(Etudiant $etudiant): float {
        return 0.1;
    }
}
