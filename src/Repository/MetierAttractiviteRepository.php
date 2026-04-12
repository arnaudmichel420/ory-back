<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Metier;
use App\Entity\MetierAttractivite;
use App\Entity\Territoire;
use App\Enum\MetierAttractiviteCodeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MetierAttractivite>
 */
class MetierAttractiviteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MetierAttractivite::class);
    }

    public function findOneByMetierTerritoireAndCode(
        Metier $metier,
        Territoire $territoire,
        MetierAttractiviteCodeEnum $codeAttractivite,
    ): ?MetierAttractivite {
        return $this->findOneBy([
            'codeOgrMetier' => $metier,
            'territoire' => $territoire,
            'codeAttractivite' => $codeAttractivite,
        ]);
    }

    /**
     * @return MetierAttractivite[]
     */
    public function findByMetierAndTerritoire(Metier $metier, Territoire $territoire): array
    {
        return $this->findBy([
            'codeOgrMetier' => $metier,
            'territoire' => $territoire,
        ]);
    }
}
