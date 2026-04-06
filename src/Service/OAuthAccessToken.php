<?php

declare(strict_types=1);

namespace App\Service;

final readonly class OAuthAccessToken
{
    private const DEFAULT_TOKEN_TYPE = 'Bearer';

    public function __construct(
        private string $value,
        private string $tokenType,
        private \DateTimeImmutable $expiresAt,
    ) {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getTokenType(): string
    {
        return '' !== $this->tokenType ? $this->tokenType : self::DEFAULT_TOKEN_TYPE;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isExpired(int $skewInSeconds = 0): bool
    {
        return $this->expiresAt->getTimestamp() <= time() + max(0, $skewInSeconds);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $value = $payload['value'] ?? null;
        $tokenType = $payload['token_type'] ?? self::DEFAULT_TOKEN_TYPE;
        $expiresAt = $payload['expires_at'] ?? null;

        if (!\is_string($value) || '' === $value || !\is_string($tokenType) || !\is_int($expiresAt)) {
            throw new \RuntimeException('Cached OAuth token payload is invalid.');
        }

        return new self(
            $value,
            $tokenType,
            (new \DateTimeImmutable())->setTimestamp($expiresAt),
        );
    }

    /**
     * @return array{value: string, token_type: string, expires_at: int}
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'token_type' => $this->getTokenType(),
            'expires_at' => $this->expiresAt->getTimestamp(),
        ];
    }
}
