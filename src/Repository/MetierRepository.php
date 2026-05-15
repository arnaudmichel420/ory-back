<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Metier\MetierListQueryDto;
use App\Entity\Etudiant;
use App\Entity\Metier;
use App\Entity\MetierAttractivite;
use App\Entity\Territoire;
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

    public function findDetailedMetier(string $codeOgr, ?Etudiant $etudiant = null): ?Metier
    {
        $queryBuilder = $this->createQueryBuilder('m')
            ->select(
                'DISTINCT m, sousDomaine, metierSecteurs, secteurs, metierCompetences, competences, metierContextes, contextes',
            )
            ->leftJoin('m.sousDomaine', 'sousDomaine')
            ->leftJoin('m.metierSecteurs', 'metierSecteurs')
            ->leftJoin('metierSecteurs.secteur', 'secteurs')
            ->leftJoin('m.metierCompetences', 'metierCompetences')
            ->leftJoin('metierCompetences.codeOgrComp', 'competences')
            ->leftJoin('m.metierContexteTravails', 'metierContextes')
            ->leftJoin('metierContextes.codeOgrContexte', 'contextes')
            ->andWhere('m.codeOgr = :codeOgr')
            ->setParameter('codeOgr', $codeOgr);

        if ($etudiant instanceof Etudiant) {
            $queryBuilder
                ->addSelect('saved_etudiant')
                ->leftJoin('m.etudiants', 'saved_etudiant', 'WITH', 'saved_etudiant = :etudiant')
                ->setParameter('etudiant', $etudiant);
        }

        /** @var ?Metier $metier */
        $metier = $queryBuilder->getQuery()->getOneOrNullResult();

        if ($metier instanceof Metier) {
            $metier->setSaved(
                $etudiant instanceof Etudiant && !$metier->getEtudiants()->isEmpty(),
            );
        }

        return $metier;
    }

    /**
     * @return array{items: list<Metier>, total: int}
     */
    public function paginateMetier(MetierListQueryDto $query, ?Etudiant $etudiant = null): array
    {
        $itemsQueryBuilder = $this->createQueryBuilder('m')
            ->select('DISTINCT m, ms_list, s_list')
            ->leftJoin('m.metierSecteurs', 'ms_list')
            ->leftJoin('ms_list.secteur', 's_list');

        if ($etudiant instanceof Etudiant) {
            $itemsQueryBuilder
                ->addSelect('saved_etudiant')
                ->leftJoin('m.etudiants', 'saved_etudiant', 'WITH', 'saved_etudiant = :etudiant')
                ->setParameter('etudiant', $etudiant);
        }

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

        foreach ($items as $metier) {
            $metier->setSaved(
                $etudiant instanceof Etudiant && !$metier->getEtudiants()->isEmpty(),
            );
        }

        return [
            'items' => $items,
            'total' => \count($paginator),
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

    /**
     * @return list<array{codeOgrMetier: string, scoreAttractivite: float}>
     */
    public function findTopAttractiveScoresForTerritoire(Territoire $territoire): array
    {
        $rows = $this->getEntityManager()
            ->createQueryBuilder()
            ->from(MetierAttractivite::class, 'ma')
            ->select(
                'IDENTITY(ma.codeOgrMetier) as codeOgrMetier',
                "SUM(
                    CASE ma.codeAttractivite
                        WHEN 'PERSPECTIVE' THEN ma.valeur * 0.30
                        WHEN 'INT_EMB' THEN ma.valeur * 0.25
                        WHEN 'DUR_EMPL' THEN ma.valeur * 0.15
                        WHEN 'ATTR_SALARIALE' THEN ma.valeur * 0.15
                        WHEN 'MAIN_OEUVRE' THEN ma.valeur * 0.10
                        WHEN 'MISMATCH_GEO' THEN ma.valeur * -0.15
                        WHEN 'COND_TRAVAIL' THEN ma.valeur * -0.05
                        ELSE 0
                    END
                ) as scoreAttractivite"
            )
            ->where('ma.territoire = :territoire')
            ->setParameter('territoire', $territoire)
            ->groupBy('ma.codeOgrMetier')
            ->orderBy('scoreAttractivite', 'DESC')
            ->getQuery()
            ->getArrayResult();

        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'codeOgrMetier' => (string) $row['codeOgrMetier'],
                'scoreAttractivite' => round((float) $row['scoreAttractivite'] / 5.0, 2),
            ];
        }

        return $result;
    }

    /**
     * @param list<string> $metiers
     *
     * @return array<string, list<int>>
     */
    public function getSecteursForMetiers(array $metiers): array
    {
        if ([] === $metiers) {
            return [];
        }

        $rows = $this->createQueryBuilder('m')
            ->select('DISTINCT IDENTITY(ms.secteur) as secteur', 'm.codeOgr')
            ->join('m.metierSecteurs', 'ms')
            ->where('m.codeOgr IN (:metiers)')
            ->setParameter('metiers', $metiers)
            ->getQuery()
            ->getArrayResult();

        $secteursByMetier = [];

        foreach ($rows as $row) {
            $secteursByMetier[(string) $row['codeOgr']][] = (int) $row['secteur'];
        }

        return $secteursByMetier;
    }

    /**
     * @param list<string> $metiers
     *
     * @return array<string, list<int>>
     */
    public function getCentresInteretForMetiers(array $metiers): array
    {
        if ([] === $metiers) {
            return [];
        }

        $rows = $this->createQueryBuilder('m')
            ->select('DISTINCT IDENTITY(mci.centreInteret) as centreInteret', 'm.codeOgr')
            ->join('m.metierCentreInterets', 'mci')
            ->where('m.codeOgr IN (:metiers)')
            ->setParameter('metiers', $metiers)
            ->getQuery()
            ->getArrayResult();

        $centresInteretByMetier = [];

        foreach ($rows as $row) {
            $centresInteretByMetier[(string) $row['codeOgr']][] = (int) $row['centreInteret'];
        }

        return $centresInteretByMetier;
    }

    /**
     * @param list<string> $metiers
     *
     * @return array<string, list<string>>
     */
    public function getContextesTravailForMetiers(array $metiers): array
    {
        if ([] === $metiers) {
            return [];
        }

        $rows = $this->createQueryBuilder('m')
            ->select('DISTINCT IDENTITY(mct.codeOgrContexte) as contexteTravail', 'm.codeOgr')
            ->join('m.metierContexteTravails', 'mct')
            ->where('m.codeOgr IN (:metiers)')
            ->setParameter('metiers', $metiers)
            ->getQuery()
            ->getArrayResult();

        $contextesTravailByMetier = [];

        foreach ($rows as $row) {
            $contextesTravailByMetier[(string) $row['codeOgr']][] = (string) $row['contexteTravail'];
        }

        return $contextesTravailByMetier;
    }

    /**
     * @param list<string> $metiers
     *
     * @return array<string, list<string>>
     */
    public function getCompetenceForMetiers(array $metiers): array
    {
        if ([] === $metiers) {
            return [];
        }

        $rows = $this->createQueryBuilder('m')
            ->select('DISTINCT IDENTITY(mc.codeOgrComp) as competence', 'm.codeOgr')
            ->join('m.metierCompetences', 'mc')
            ->where('m.codeOgr IN (:metiers)')
            ->setParameter('metiers', $metiers)
            ->getQuery()
            ->getArrayResult();

        $competencesByMetier = [];

        foreach ($rows as $row) {
            $competencesByMetier[(string) $row['codeOgr']][] = (string) $row['competence'];
        }

        return $competencesByMetier;
    }

    /**
     * @return list<array{codeOgrMetier: string, scoreAttractivite: float}>
     */
    public function findAllNoAttractivity(): array
    {
        $rows = $this->createQueryBuilder('m')
            ->select('m.codeOgr as codeOgrMetier', '0 as scoreAttractivite')
            ->getQuery()
            ->getArrayResult();

        return array_map(
            static fn (array $row): array => [
                'codeOgrMetier' => (string) $row['codeOgrMetier'],
                'scoreAttractivite' => (float) $row['scoreAttractivite'],
            ],
            $rows,
        );
    }
}
