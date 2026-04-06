<?php

declare(strict_types=1);

namespace App\Service;

use App\Config\OAuthServerConfig;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class OAuthTokenProvider
{
    private const CACHE_KEY_PREFIX = 'external_api.oauth.access_token';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheItemPoolInterface $cache,
        private readonly OAuthServerConfig $config,
    ) {
    }

    public function getAccessToken(?string $scope = null): OAuthAccessToken
    {
        $normalizedScope = $this->normalizeScope($scope);
        $cachedToken = $this->getCachedToken($normalizedScope);
        if (null !== $cachedToken && !$cachedToken->isExpired()) {
            return $cachedToken;
        }

        $freshToken = $this->requestToken($normalizedScope);
        $this->saveToken($freshToken, $normalizedScope);

        return $freshToken;
    }

    public function getAuthorizationHeaderValue(?string $scope = null): string
    {
        $token = $this->getAccessToken($scope);

        return \sprintf('%s %s', $token->getTokenType(), $token->getValue());
    }

    private function getCachedToken(string $scope): ?OAuthAccessToken
    {
        $cacheItem = $this->cache->getItem($this->getCacheKey($scope));
        if (!$cacheItem->isHit()) {
            return null;
        }

        $payload = $cacheItem->get();
        if (!\is_array($payload)) {
            return null;
        }

        try {
            return OAuthAccessToken::fromArray($payload);
        } catch (\RuntimeException) {
            return null;
        }
    }

    private function saveToken(OAuthAccessToken $token, string $scope): void
    {
        $cacheItem = $this->cache->getItem($this->getCacheKey($scope));
        $cacheItem->set($token->toArray());
        $cacheItem->expiresAt($token->getExpiresAt());
        $this->cache->save($cacheItem);
    }

    private function requestToken(string $scope): OAuthAccessToken
    {
        $body = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->config->getClientId(),
            'client_secret' => $this->config->getClientSecret(),
        ];

        if ('' !== $scope) {
            $body['scope'] = $scope;
        }

        try {
            $response = $this->httpClient->request('POST', $this->config->getTokenUrl(), [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => $body,
            ]);

            /** @var array<string, mixed> $payload */
            $payload = $response->toArray(false);
        } catch (TransportExceptionInterface $exception) {
            throw new \RuntimeException('Unable to reach OAuth token endpoint.', previous: $exception);
        }

        $accessToken = $payload['access_token'] ?? null;
        $tokenType = $payload['token_type'] ?? 'Bearer';
        $expiresIn = $payload['expires_in'] ?? null;

        if (!\is_string($accessToken) || '' === $accessToken) {
            throw new \RuntimeException('OAuth token response does not contain a valid access_token.');
        }

        if (!\is_string($tokenType) || '' === $tokenType) {
            $tokenType = 'Bearer';
        }

        if (!\is_int($expiresIn) && !\is_float($expiresIn) && !\is_string($expiresIn)) {
            throw new \RuntimeException('OAuth token response does not contain a valid expires_in value.');
        }

        $expiresInSeconds = (int) $expiresIn;
        if ($expiresInSeconds <= 0) {
            throw new \RuntimeException('OAuth token response returned a non-positive expires_in value.');
        }

        return new OAuthAccessToken(
            $accessToken,
            $tokenType,
            (new \DateTimeImmutable())->add(new \DateInterval(\sprintf('PT%dS', $expiresInSeconds))),
        );
    }

    private function normalizeScope(?string $scope): string
    {
        return trim((string) $scope);
    }

    private function getCacheKey(string $scope): string
    {
        if ('' === $scope) {
            return self::CACHE_KEY_PREFIX.'.default';
        }

        return self::CACHE_KEY_PREFIX.'.'.hash('sha256', $scope);
    }
}
