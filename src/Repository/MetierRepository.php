<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Metier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Metier>
 */
class MetierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Metier::class);
    }

    /**
     * @return list<array{codeOgr:string, codeRome:string}>
     */
    public function findAttractiviteImportCandidates(): array
    {
        $rows = $this->createQueryBuilder('m')
            ->select('m.codeOgr AS codeOgr, m.codeRome AS codeRome')
            ->andWhere('m.codeRome IS NOT NULL')
            ->getQuery()
            ->getArrayResult();

        return array_values(array_filter(
            array_map(
                static fn (mixed $row): ?array => \is_array($row)
                    && isset($row['codeOgr'], $row['codeRome'])
                    && \is_string($row['codeOgr'])
                    && '' !== $row['codeOgr']
                    && \is_string($row['codeRome'])
                    && '' !== $row['codeRome']
                    ? [
                        'codeOgr' => $row['codeOgr'],
                        'codeRome' => $row['codeRome'],
                    ]
                    : null,
                $rows,
            ),
        ));
    }

    public function countAttractiviteImportCandidates(): int
    {
        /** @var int|string $count */
        $count = $this->createQueryBuilder('m')
            ->select('COUNT(m.codeOgr)')
            ->andWhere('m.codeRome IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $count;
    }
}
