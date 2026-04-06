<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Territoire;
use App\Config\OAuthServerConfig;
use App\Enum\TerritoireCodeTypeTerritoireEnum;
use App\Repository\TerritoireRepository;
use App\Service\OAuthApiClient;
use App\Service\OAuthTokenProvider;
use App\Service\TerritoireServices;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class TerritoireServicesTest extends TestCase
{
    private OAuthApiClient $oAuthApiClient;
    private EntityManagerInterface&MockObject $entityManager;
    private TerritoireRepository $territoireRepository;
    private TerritoireServices $service;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->territoireRepository = $this->createStub(TerritoireRepository::class);
        $this->oAuthApiClient = $this->createOAuthApiClient(
            $this->createStub(HttpClientInterface::class),
        );

        $this->service = new TerritoireServices(
            $this->oAuthApiClient,
            $this->entityManager,
            $this->territoireRepository,
        );
    }

    public function testCreeMetAJourEtRelieLesParentsLorsDeLImportDesTerritoires(): void
    {
        $existingFrance = (new Territoire())
            ->setCodeTypeTerritoire(TerritoireCodeTypeTerritoireEnum::NAT)
            ->setCodeTerritoire('FR')
            ->setLibelleTerritoire('Ancienne France');

        $persistedTerritoires = [];

        $this->territoireRepository
            ->method('findOneByTypeAndCode')
            ->willReturnCallback(
                static fn (TerritoireCodeTypeTerritoireEnum $type, string $code): ?Territoire => $type === TerritoireCodeTypeTerritoireEnum::NAT && $code === 'FR'
                    ? $existingFrance
                    : null
            );

        $this->entityManager
            ->expects($this->exactly(4))
            ->method('persist')
            ->willReturnCallback(static function (object $entity) use (&$persistedTerritoires): void {
                if ($entity instanceof Territoire) {
                    $persistedTerritoires[] = $entity;
                }
            });

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $stats = $this->service->createCodeTerritoire([
            'NAT' => [
                [
                    'codeTypeTerritoire' => 'NAT',
                    'codeTerritoire' => 'FR',
                    'libelleTerritoire' => 'France',
                ],
                [
                    'codeTypeTerritoire' => 'NAT',
                    'codeTerritoire' => 'DOM',
                    'libelleTerritoire' => 'Departements d outre-mer',
                    'codeTypeTerritoireParent' => 'NAT',
                    'codeTerritoireParent' => 'FR',
                ],
            ],
            'REG' => [
                [
                    'codeTypeTerritoire' => 'REG',
                    'codeTerritoire' => '01',
                    'libelleTerritoire' => 'Guadeloupe',
                    'codeTypeTerritoireParent' => 'NAT',
                    'codeTerritoireParent' => 'DOM',
                ],
            ],
            'DEP' => [
                [
                    'codeTypeTerritoire' => 'DEP',
                    'codeTerritoire' => '01',
                    'libelleTerritoire' => 'Ain',
                    'codeTypeTerritoireParent' => 'REG',
                    'codeTerritoireParent' => '01',
                ],
            ],
        ]);

        self::assertSame([
            'created' => 3,
            'updated' => 1,
            'skipped_invalid' => 0,
            'parent_bound' => 3,
            'parent_missing' => 0,
            'total_received' => 4,
        ], $stats);

        self::assertSame('France', $existingFrance->getLibelleTerritoire());

        $territoiresByKey = $this->indexTerritoiresByKey($persistedTerritoires);

        self::assertArrayHasKey('NAT:FR', $territoiresByKey);
        self::assertArrayHasKey('NAT:DOM', $territoiresByKey);
        self::assertArrayHasKey('REG:01', $territoiresByKey);
        self::assertArrayHasKey('DEP:01', $territoiresByKey);

        self::assertSame($territoiresByKey['NAT:FR'], $territoiresByKey['NAT:DOM']->getCodeTerritoireParent());
        self::assertSame($territoiresByKey['NAT:DOM'], $territoiresByKey['REG:01']->getCodeTerritoireParent());
        self::assertSame($territoiresByKey['REG:01'], $territoiresByKey['DEP:01']->getCodeTerritoireParent());
    }

    public function testRecupereLesTerritoiresDepuisLApiEtRetourneDesStatistiquesStructurees(): void
    {
        $persistedTerritoires = [];

        $responses = [
            'NAT' => $this->createApiResponse([
                'territoires' => [
                    [
                        'codeTypeTerritoire' => 'NAT',
                        'codeTerritoire' => 'FR',
                        'libelleTerritoire' => 'France',
                    ],
                ],
            ]),
            'REG' => $this->createApiResponse([
                'territoires' => [
                    [
                        'codeTypeTerritoire' => 'REG',
                        'codeTerritoire' => '11',
                        'libelleTerritoire' => 'Ile-de-France',
                        'codeTypeTerritoireParent' => 'NAT',
                        'codeTerritoireParent' => 'FR',
                    ],
                ],
            ]),
            'DEP' => $this->createApiResponse([
                'territoires' => [
                    [
                        'codeTypeTerritoire' => 'DEP',
                        'codeTerritoire' => '75',
                        'libelleTerritoire' => 'Paris',
                        'codeTypeTerritoireParent' => 'REG',
                        'codeTerritoireParent' => '11',
                    ],
                ],
            ]),
        ];

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects($this->exactly(3))
            ->method('request')
            ->willReturnCallback(function (string $method, string $uri) use ($responses): ResponseInterface {
                self::assertSame('GET', $method);
                $type = basename($uri);
                self::assertArrayHasKey($type, $responses);

                return $responses[$type];
            });

        $service = new TerritoireServices(
            $this->createOAuthApiClient($httpClient),
            $this->entityManager,
            $this->territoireRepository,
        );

        $this->territoireRepository
            ->method('findOneByTypeAndCode')
            ->willReturn(null);

        $this->entityManager
            ->expects($this->once())
            ->method('wrapInTransaction')
            ->willReturnCallback(static fn (callable $callback): mixed => $callback());

        $this->entityManager
            ->expects($this->exactly(3))
            ->method('persist')
            ->willReturnCallback(static function (object $entity) use (&$persistedTerritoires): void {
                if ($entity instanceof Territoire) {
                    $persistedTerritoires[] = $entity;
                }
            });

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $service->scrapTerritoire();

        self::assertTrue($result['success']);
        self::assertSame('Import des territoires termine.', $result['message']);
        self::assertSame([
            'NAT' => 1,
            'REG' => 1,
            'DEP' => 1,
        ], $result['fetched']);
        self::assertSame([
            'created' => 3,
            'updated' => 0,
            'skipped_invalid' => 0,
            'parent_bound' => 2,
            'parent_missing' => 0,
            'total_received' => 3,
        ], $result['import']);

        $territoiresByKey = $this->indexTerritoiresByKey($persistedTerritoires);

        self::assertSame($territoiresByKey['NAT:FR'], $territoiresByKey['REG:11']->getCodeTerritoireParent());
        self::assertSame($territoiresByKey['REG:11'], $territoiresByKey['DEP:75']->getCodeTerritoireParent());
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function createApiResponse(array $payload): ResponseInterface
    {
        $response = $this->createStub(ResponseInterface::class);
        $response
            ->method('toArray')
            ->willReturn($payload);

        return $response;
    }

    /**
     * @param list<Territoire> $territoires
     * @return array<string, Territoire>
     */
    private function indexTerritoiresByKey(array $territoires): array
    {
        $indexed = [];

        foreach ($territoires as $territoire) {
            $type = $territoire->getCodeTypeTerritoire();
            $code = $territoire->getCodeTerritoire();

            if ($type === null || $code === null) {
                continue;
            }

            $indexed[sprintf('%s:%s', $type->value, $code)] = $territoire;
        }

        return $indexed;
    }

    private function createOAuthApiClient(HttpClientInterface $httpClient): OAuthApiClient
    {
        $cacheItem = $this->createStub(CacheItemInterface::class);
        $cacheItem
            ->method('isHit')
            ->willReturn(true);
        $cacheItem
            ->method('get')
            ->willReturn([
                'value' => 'test-token',
                'token_type' => 'Bearer',
                'expires_at' => time() + 3600,
            ]);

        $cachePool = $this->createStub(CacheItemPoolInterface::class);
        $cachePool
            ->method('getItem')
            ->willReturn($cacheItem);

        $tokenProvider = new OAuthTokenProvider(
            $httpClient,
            $cachePool,
            new OAuthServerConfig(
                'https://oauth.example.test/token',
                'client-id',
                'client-secret',
            ),
        );

        return new OAuthApiClient(
            $httpClient,
            $tokenProvider,
            10.0,
        );
    }
}
