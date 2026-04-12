<?php

declare(strict_types=1);

namespace App\Service\MetierAttractivite;

use App\Entity\Metier;
use App\Entity\MetierAttractivite;
use App\Entity\Territoire;
use App\Enum\MetierAttractiviteCodeEnum;
use App\Repository\MetierAttractiviteRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class MetierAttractivitePersister
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MetierAttractiviteRepository $repository,
    ) {
    }

    /**
     * @param array<string, int> $snapshot
     *
     * @return array{created:int,updated:int,deleted:int}
     */
    public function persistSnapshot(Metier $metier, Territoire $territoire, array $snapshot): array
    {
        $existingByCode = [];
        foreach ($this->repository->findByMetierAndTerritoire($metier, $territoire) as $existing) {
            $code = $existing->getCodeAttractivite();
            if (null !== $code) {
                $existingByCode[$code->value] = $existing;
            }
        }

        $created = 0;
        $updated = 0;

        foreach ($snapshot as $code => $value) {
            $enumCode = MetierAttractiviteCodeEnum::from($code);
            $entity = $existingByCode[$code] ?? null;

            if (!$entity instanceof MetierAttractivite) {
                $entity = new MetierAttractivite();
                $entity
                    ->setCodeOgrMetier($metier)
                    ->setTerritoire($territoire)
                    ->setCodeAttractivite($enumCode);
                $this->entityManager->persist($entity);
                ++$created;
            } else {
                ++$updated;
            }

            $entity->setValeur($value);
            unset($existingByCode[$code]);
        }

        $deleted = 0;
        foreach ($existingByCode as $obsolete) {
            $this->entityManager->remove($obsolete);
            ++$deleted;
        }

        $this->entityManager->flush();

        return [
            'created' => $created,
            'updated' => $updated,
            'deleted' => $deleted,
        ];
    }
}
