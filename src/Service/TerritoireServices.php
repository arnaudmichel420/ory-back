<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Territoire;
use App\Enum\TerritoireCodeTypeTerritoireEnum;
use App\Repository\TerritoireRepository;
use Doctrine\ORM\EntityManagerInterface;

class TerritoireServices
{
    private const CODES_TERRITOIRE = ['NAT', 'REG', 'DEP'];

    public function __construct(
        private OAuthApiClient $oAuthApiClient,
        private EntityManagerInterface $entityManager,
        private TerritoireRepository $territoireRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function scrapTerritoire(): array
    {
        $responses = [];
        $fetched = [];

        foreach (self::CODES_TERRITOIRE as $code) {
            $response = $this->oAuthApiClient->get(
                \sprintf('https://api.francetravail.io/partenaire/stats-offres-demandes-emploi/v1/referentiel/territoires/%s', $code),
                [],
                'offresetdemandesemploi api_stats-offres-demandes-emploiv1'
            );

            $payload = $response->toArray();
            if (!isset($payload['territoires']) || !\is_array($payload['territoires'])) {
                throw new \RuntimeException(\sprintf('La reponse API pour le type "%s" ne contient pas de liste de territoires valide.', $code));
            }

            $responses[$code] = $payload['territoires'];
            $fetched[$code] = \count($payload['territoires']);
        }

        try {
            /** @var array{created:int,updated:int,skipped_invalid:int,parent_bound:int,parent_missing:int,total_received:int} $importStats */
            $importStats = $this->entityManager->wrapInTransaction(
                fn (): array => $this->createCodeTerritoire($responses)
            );
        } catch (\Throwable $exception) {
            $this->entityManager->clear();

            throw new \RuntimeException('L\'import des territoires a echoue et a ete annule.', previous: $exception);
        }

        return [
            'success' => true,
            'message' => 'Import des territoires termine.',
            'fetched' => $fetched,
            'import' => $importStats,
        ];
    }

    /**
     * @param array<string, array<int, array<string, mixed>>> $territoiresParType
     *
     * @return array{created:int,updated:int,skipped_invalid:int,parent_bound:int,parent_missing:int,total_received:int}
     */
    public function createCodeTerritoire(array $territoiresParType): array
    {
        $territoiresEnAjout = [];
        $createdCount = 0;
        $updatedCount = 0;
        $skippedInvalidCount = 0;
        $parentBoundCount = 0;
        $parentMissingCount = 0;
        $totalReceivedCount = 0;

        foreach (self::CODES_TERRITOIRE as $typeTerritoire) {
            foreach ($this->getTerritoiresDuType($territoiresParType, $typeTerritoire) as $territoireApi) {
                ++$totalReceivedCount;

                if (!$this->isTerritoirePayloadValid($territoireApi)) {
                    ++$skippedInvalidCount;
                    continue;
                }

                $codeTypeTerritoire = TerritoireCodeTypeTerritoireEnum::from($territoireApi['codeTypeTerritoire']);
                $codeTerritoire = $territoireApi['codeTerritoire'];
                $territoireKey = $this->getTerritoireKey($codeTypeTerritoire, $codeTerritoire);
                $territoire = $territoiresEnAjout[$territoireKey]
                    ?? $this->territoireRepository->findOneByTypeAndCode($codeTypeTerritoire, $codeTerritoire);

                if (!$territoire instanceof Territoire) {
                    $territoire = new Territoire();
                    $territoire->setCodeTerritoire($codeTerritoire);
                    ++$createdCount;
                } else {
                    ++$updatedCount;
                }

                $territoire->setCodeTypeTerritoire($codeTypeTerritoire);
                $territoire->setLibelleTerritoire($territoireApi['libelleTerritoire']);
                $territoire->setCodeTypeTerritoireParent(
                    isset($territoireApi['codeTypeTerritoireParent']) && \is_string($territoireApi['codeTypeTerritoireParent'])
                        ? TerritoireCodeTypeTerritoireEnum::from($territoireApi['codeTypeTerritoireParent'])
                        : null
                );

                $this->entityManager->persist($territoire);
                $territoiresEnAjout[$territoireKey] = $territoire;
            }
        }

        foreach (self::CODES_TERRITOIRE as $typeTerritoire) {
            foreach ($this->getTerritoiresDuType($territoiresParType, $typeTerritoire) as $territoireApi) {
                if (!$this->isTerritoirePayloadValid($territoireApi)) {
                    continue;
                }

                $codeTypeTerritoire = TerritoireCodeTypeTerritoireEnum::from($territoireApi['codeTypeTerritoire']);
                $territoireKey = $this->getTerritoireKey($codeTypeTerritoire, $territoireApi['codeTerritoire']);
                $territoire = $territoiresEnAjout[$territoireKey] ?? null;
                if (!$territoire instanceof Territoire) {
                    continue;
                }

                $codeParent = $territoireApi['codeTerritoireParent'] ?? null;
                if (!\is_string($codeParent) || '' === $codeParent) {
                    $territoire->setCodeTerritoireParent(null);
                    continue;
                }

                $codeTypeParent = isset($territoireApi['codeTypeTerritoireParent']) && \is_string($territoireApi['codeTypeTerritoireParent'])
                    ? TerritoireCodeTypeTerritoireEnum::from($territoireApi['codeTypeTerritoireParent'])
                    : null;

                if (null === $codeTypeParent) {
                    ++$parentMissingCount;
                    continue;
                }

                $parentKey = $this->getTerritoireKey($codeTypeParent, $codeParent);
                $parent = $territoiresEnAjout[$parentKey]
                    ?? $this->territoireRepository->findOneByTypeAndCode($codeTypeParent, $codeParent);

                if ($parent instanceof Territoire) {
                    $territoire->setCodeTerritoireParent($parent);
                    ++$parentBoundCount;
                } else {
                    ++$parentMissingCount;
                }
            }
        }

        $this->entityManager->flush();

        return [
            'created' => $createdCount,
            'updated' => $updatedCount,
            'skipped_invalid' => $skippedInvalidCount,
            'parent_bound' => $parentBoundCount,
            'parent_missing' => $parentMissingCount,
            'total_received' => $totalReceivedCount,
        ];
    }

    private function isTerritoirePayloadValid(mixed $territoireApi): bool
    {
        if (!\is_array($territoireApi)) {
            return false;
        }

        if (!isset($territoireApi['codeTerritoire'], $territoireApi['codeTypeTerritoire'], $territoireApi['libelleTerritoire'])) {
            return false;
        }

        if (!\is_string($territoireApi['codeTerritoire']) || '' === $territoireApi['codeTerritoire']) {
            return false;
        }

        if (!\is_string($territoireApi['codeTypeTerritoire']) || '' === $territoireApi['codeTypeTerritoire']) {
            return false;
        }

        if (!\in_array($territoireApi['codeTypeTerritoire'], TerritoireCodeTypeTerritoireEnum::values(), true)) {
            return false;
        }

        if (!\is_string($territoireApi['libelleTerritoire']) || '' === $territoireApi['libelleTerritoire']) {
            return false;
        }

        if (
            isset($territoireApi['codeTypeTerritoireParent'])
            && (!\is_string($territoireApi['codeTypeTerritoireParent'])
                || !\in_array($territoireApi['codeTypeTerritoireParent'], TerritoireCodeTypeTerritoireEnum::values(), true))
        ) {
            return false;
        }

        if (
            isset($territoireApi['codeTerritoireParent'])
            && !\is_string($territoireApi['codeTerritoireParent'])
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param array<string, array<int, array<string, mixed>>> $territoiresParType
     *
     * @return array<int, array<string, mixed>>
     */
    private function getTerritoiresDuType(array $territoiresParType, string $typeTerritoire): array
    {
        return $territoiresParType[$typeTerritoire] ?? [];
    }

    private function getTerritoireKey(
        TerritoireCodeTypeTerritoireEnum $codeTypeTerritoire,
        string $codeTerritoire,
    ): string {
        return \sprintf('%s:%s', $codeTypeTerritoire->value, $codeTerritoire);
    }
}
