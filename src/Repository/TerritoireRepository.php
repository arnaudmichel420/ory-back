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

    /**
     * @return Territoire[]
     */
    public function findByType(TerritoireCodeTypeTerritoireEnum $codeTypeTerritoire): array
    {
        return $this->findBy([
            'codeTypeTerritoire' => $codeTypeTerritoire,
        ]);
    }

    /**
     * @return list<string>
     */
    public function findCodesByType(TerritoireCodeTypeTerritoireEnum $codeTypeTerritoire): array
    {
        $rows = $this->createQueryBuilder('t')
            ->select('t.codeTerritoire AS codeTerritoire')
            ->andWhere('t.codeTypeTerritoire = :type')
            ->andWhere('t.codeTerritoire IS NOT NULL')
            ->setParameter('type', $codeTypeTerritoire)
            ->getQuery()
            ->getArrayResult();

        return array_values(array_filter(
            array_map(
                static fn (mixed $row): ?string => \is_array($row)
                    && isset($row['codeTerritoire'])
                    && \is_string($row['codeTerritoire'])
                    && '' !== $row['codeTerritoire']
                    ? $row['codeTerritoire']
                    : null,
                $rows,
            ),
        ));
    }

    public function countByType(TerritoireCodeTypeTerritoireEnum $codeTypeTerritoire): int
    {
        /** @var int|string $count */
        $count = $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.codeTypeTerritoire = :type')
            ->setParameter('type', $codeTypeTerritoire)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $count;
    }
}
