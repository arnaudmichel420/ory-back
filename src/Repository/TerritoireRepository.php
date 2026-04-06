<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Territoire;
use App\Enum\TerritoireCodeTypeTerritoireEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Territoire>
 */
class TerritoireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Territoire::class);
    }

    public function findOneByTypeAndCode(
        TerritoireCodeTypeTerritoireEnum $codeTypeTerritoire,
        string $codeTerritoire,
    ): ?Territoire {
        return $this->findOneBy([
            'codeTypeTerritoire' => $codeTypeTerritoire,
            'codeTerritoire' => $codeTerritoire,
        ]);
    }
}
