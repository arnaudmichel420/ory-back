<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Config\OAuthServerConfig;
use App\Service\OAuthApiClient;
use App\Service\OAuthTokenProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class OAuthApiClientTest extends TestCase
{
    private HttpClientInterface&MockObject $httpClient;
    private OAuthApiClient $client;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->client = $this->createOAuthApiClient($this->httpClient);
    }

    public function testAjouteLeBearerTokenEtLeTimeoutParDefautSurUnGet(): void
    {
        $response = $this->createStub(ResponseInterface::class);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'territoires/NAT',
                [
                    'query' => ['page' => 1],
                    'headers' => [
                        'Authorization' => 'Bearer test-token',
                        'Accept' => 'application/json',
                    ],
                    'timeout' => 10.0,
                ],
            )
            ->willReturn($response);

        $actualResponse = $this->client->get('territoires/NAT', ['page' => 1], 'mon-scope');

        self::assertSame($response, $actualResponse);
    }

    public function testConserveLeTimeoutEtLeAcceptFournisDansLesOptions(): void
    {
        $response = $this->createStub(ResponseInterface::class);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'territoires',
                [
                    'json' => ['code' => 'FR'],
                    'headers' => [
                        'Accept' => 'application/xml',
                        'Authorization' => 'Bearer test-token',
                    ],
                    'timeout' => 3.5,
                ],
            )
            ->willReturn($response);

        $actualResponse = $this->client->request('POST', 'territoires', [
            'json' => ['code' => 'FR'],
            'headers' => ['Accept' => 'application/xml'],
            'timeout' => 3.5,
        ]);

        self::assertSame($response, $actualResponse);
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
