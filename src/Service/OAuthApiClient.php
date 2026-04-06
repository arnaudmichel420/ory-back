<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final readonly class OAuthApiClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private OAuthTokenProvider $tokenProvider,
        private float $timeoutInSeconds = 10.0,
    ) {
    }

    /**
     * @param array<string, mixed> $options
     */
    public function request(string $method, string $uri, array $options = [], ?string $scope = null): ResponseInterface
    {
        $headers = $options['headers'] ?? [];
        if (!\is_array($headers)) {
            $headers = [];
        }

        $headers['Authorization'] = $this->tokenProvider->getAuthorizationHeaderValue($scope);
        $headers['Accept'] = $headers['Accept'] ?? 'application/json';

        $options['headers'] = $headers;
        $options['timeout'] = $options['timeout'] ?? ($this->timeoutInSeconds > 0 ? $this->timeoutInSeconds : 10.0);

        return $this->httpClient->request($method, ltrim($uri, '/'), $options);
    }

    /**
     * @param array<string, mixed> $query
     */
    public function get(string $uri, array $query = [], ?string $scope = null): ResponseInterface
    {
        $options = [];
        if ([] !== $query) {
            $options['query'] = $query;
        }

        return $this->request('GET', $uri, $options, $scope);
    }

    /**
     * @param array<string, mixed> $json
     */
    public function post(string $uri, array $json = [], ?string $scope = null): ResponseInterface
    {
        $options = [];
        if ([] !== $json) {
            $options['json'] = $json;
        }

        return $this->request('POST', $uri, $options, $scope);
    }
}
