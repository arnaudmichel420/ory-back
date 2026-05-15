<?php

declare(strict_types=1);

namespace App\Service\Recommendation;

use App\Entity\Etudiant;
use App\Entity\Territoire;
use App\Repository\MetierRepository;
use App\Repository\TerritoireRepository;

final class AttractiviteCalculatorService
{
    public function __construct(private MetierRepository $metierRepository, private TerritoireRepository $territoireRepository)
    {
    }

    /**
     * @return list<array{codeOgrMetier: string, scoreAttractivite: float}>
     */
    public function getAttractiveMetier(Etudiant $etudiant): array
    {
        $codePostal = $etudiant->getCodePostal();

        if (empty($codePostal)) {
            return $this->metierRepository->findAllNoAttractivity();
        }

        $territoire = $this->getTerritoireByCodePostal($codePostal);

        if (empty($territoire)) {
            return $this->metierRepository->findAllNoAttractivity();
        }

        return $this->metierRepository->findTopAttractiveScoresForTerritoire($territoire);
    }

    public function getTerritoireByCodePostal(string $codePostal): ?Territoire
    {
        if (str_starts_with($codePostal, '97') || str_starts_with($codePostal, '98')) {
            $departmentNumber = 3;
        } else {
            $departmentNumber = 2;
        }

        $department = substr($codePostal, 0, $departmentNumber);

        return $this->territoireRepository->findOneBy(['codeTypeTerritoire' => 'DEP', 'codeTerritoire' => $department]);
    }
}
