<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\MetierAttractivite;

use App\Config\OAuthServerConfig;
use App\Service\MetierAttractivite\MetierAttractiviteApiRateLimiter;
use App\Service\MetierAttractivite\MetierAttractiviteApiRetryableException;
use App\Service\MetierAttractivite\MetierAttractiviteApiService;
use App\Service\OAuthApiClient;
use App\Service\OAuthTokenProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class MetierAttractiviteApiServiceTest extends TestCase
{
    private HttpClientInterface&MockObject $httpClient;
    private MetierAttractiviteApiService $service;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->service = new MetierAttractiviteApiService(
            $this->createOAuthApiClient($this->httpClient),
            new MetierAttractiviteApiRateLimiter(sys_get_temp_dir()),
        );
    }

    public function testParseLesValeursValidesEtIgnoreLesLignesInvalides(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getStatusCode')
            ->willReturn(200);
        $response
            ->expects($this->once())
            ->method('toArray')
            ->with(false)
            ->willReturn([
                'listeValeursParPeriode' => [
                    [
                        'codeTypeTerritoire' => 'DEP',
                        'codeTypeActivite' => 'ROME',
                        'codeNomenclature' => 'INT_EMB',
                        'valeurPrincipaleNombre' => 4,
                    ],
                    [
                        'codeTypeTerritoire' => 'DEP',
                        'codeTypeActivite' => 'ROME',
                        'codeNomenclature' => 'COND_TRAVAIL',
                        'valeurPrincipaleNombre' => '2',
                    ],
                    [
                        'codeTypeTerritoire' => 'REG',
                        'codeTypeActivite' => 'ROME',
                        'codeNomenclature' => 'INT_EMB',
                        'valeurPrincipaleNombre' => 5,
                    ],
                    [
                        'codeTypeTerritoire' => 'DEP',
                        'codeTypeActivite' => 'ROME',
                        'codeNomenclature' => 'UNKNOWN',
                        'valeurPrincipaleNombre' => 5,
                    ],
                    [
                        'codeTypeTerritoire' => 'DEP',
                        'codeTypeActivite' => 'ROME',
                        'codeNomenclature' => 'PERSPECTIVE',
                        'valeurPrincipaleNombre' => null,
                    ],
                ],
            ]);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $result = $this->service->fetchSnapshot('75', 'A1203');

        $this->assertSame([
            'INT_EMB' => 4,
            'COND_TRAVAIL' => 2,
        ], $result['values']);
        $this->assertSame(3, $result['ignored']);
    }

    public function testLanceUneExceptionRetryableSurUn429EtLitRetryAfter(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getStatusCode')
            ->willReturn(429);
        $response
            ->expects($this->once())
            ->method('getHeaders')
            ->with(false)
            ->willReturn([
                'retry-after' => ['3'],
            ]);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($response);

        try {
            $this->service->fetchSnapshot('75', 'A1203');
            self::fail('Une exception retryable etait attendue.');
        } catch (MetierAttractiviteApiRetryableException $exception) {
            $this->assertSame(3000, $exception->getRetryAfterMilliseconds());
            self::assertStringContainsString('429', $exception->getMessage());
        }
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

        return new OAuthApiClient($httpClient, $tokenProvider, 10.0);
    }
}
