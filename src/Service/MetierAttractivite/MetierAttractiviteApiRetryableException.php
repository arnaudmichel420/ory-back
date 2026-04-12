<?php

declare(strict_types=1);

namespace App\Service\MetierAttractivite;

final class MetierAttractiviteApiRetryableException extends MetierAttractiviteApiException
{
    public function __construct(
        string $message,
        private readonly ?int $retryAfterMilliseconds = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function getRetryAfterMilliseconds(): ?int
    {
        return $this->retryAfterMilliseconds;
    }
}
