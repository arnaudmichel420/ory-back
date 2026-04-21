<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Metier\MetierListQueryDto;
use App\Entity\Metier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    /**
     * @return array{items: list<Metier>, total: int}
     */
    public function paginateMetier(MetierListQueryDto $query): array
    {
        $itemsQueryBuilder = $this->createQueryBuilder('m')
            ->select('DISTINCT m, ms_list, s_list')
            ->leftJoin('m.metierSecteurs', 'ms_list')
            ->leftJoin('ms_list.secteur', 's_list');

        $this->applyListFilters($itemsQueryBuilder, $query);

        $sortField = match ($query->getSortField()) {
            'libelle' => 'm.libelle',
            default => 'm.libelle',
        };

        $paginator = new Paginator(
            $itemsQueryBuilder
            ->orderBy($sortField, $query->getSortDirection())
            ->addOrderBy('m.codeOgr', 'ASC')
            ->setFirstResult($query->getOffset())
            ->setMaxResults($query->perPage)
            ->getQuery(),
            true,
        );

        /** @var list<Metier> $items */
        $items = iterator_to_array($paginator->getIterator(), false);

        return [
            'items' => $items,
            'total' => count($paginator),
        ];
    }

    private function applyListFilters(QueryBuilder $queryBuilder, MetierListQueryDto $query): void
    {
        if (null !== $query->search && '' !== trim($query->search)) {
            $queryBuilder
                ->andWhere('LOWER(m.libelle) LIKE LOWER(:search)')
                ->setParameter('search', '%'.trim($query->search).'%');
        }

        $secteurIds = $query->getSecteurIdsAsInts();
        if ([] !== $secteurIds) {
            $queryBuilder
                ->innerJoin('m.metierSecteurs', 'ms_filter')
                ->innerJoin('ms_filter.secteur', 's_filter')
                ->andWhere('s_filter.id IN (:secteurIds)')
                ->setParameter('secteurIds', $secteurIds);
        }

        if (null !== $query->transitionEco) {
            $queryBuilder
                ->andWhere('m.transitionEco = :transitionEco')
                ->setParameter('transitionEco', $query->transitionEco);
        }

        if (null !== $query->transitionNum) {
            $queryBuilder
                ->andWhere('m.transitionNum = :transitionNum')
                ->setParameter('transitionNum', $query->transitionNum);
        }

        if (null !== $query->emploiCadre) {
            $queryBuilder
                ->andWhere('m.emploiCadre = :emploiCadre')
                ->setParameter('emploiCadre', $query->emploiCadre);
        }
    }
}
