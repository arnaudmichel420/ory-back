<?php

declare(strict_types=1);

namespace App\Config;

final readonly class OAuthServerConfig
{
    public function __construct(
        private string $tokenUrl,
        private string $clientId,
        private string $clientSecret,
    ) {
    }

    public function getTokenUrl(): string
    {
        return $this->tokenUrl;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }
}
