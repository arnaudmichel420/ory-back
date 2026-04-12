<?php

declare(strict_types=1);

namespace App\Service\MetierAttractivite;

final readonly class MetierAttractiviteApiRateLimiter
{
    private const MIN_INTERVAL_MICROSECONDS = 100000;

    public function __construct(
        private string $projectDir,
    ) {
    }

    public function throttle(): void
    {
        $lockDirectory = $this->projectDir.'/var/lock';
        if (!is_dir($lockDirectory) && !mkdir($lockDirectory, 0777, true) && !is_dir($lockDirectory)) {
            throw new \RuntimeException(\sprintf('Impossible de creer le dossier de lock "%s".', $lockDirectory));
        }

        $lockPath = $lockDirectory.'/metier_attractivite_api_rate_limit.lock';
        $handle = fopen($lockPath, 'c+');
        if (false === $handle) {
            throw new \RuntimeException(\sprintf('Impossible d\'ouvrir le lock rate limit "%s".', $lockPath));
        }

        try {
            if (!flock($handle, \LOCK_EX)) {
                throw new \RuntimeException('Impossible de verrouiller le rate limiter.');
            }

            $contents = stream_get_contents($handle);
            $lastTimestamp = \is_string($contents) ? (int) trim($contents) : 0;
            $now = (int) floor(microtime(true) * 1000000);
            $nextAllowed = $lastTimestamp + self::MIN_INTERVAL_MICROSECONDS;

            if ($nextAllowed > $now) {
                usleep($nextAllowed - $now);
                $now = (int) floor(microtime(true) * 1000000);
            }

            rewind($handle);
            ftruncate($handle, 0);
            fwrite($handle, (string) $now);
            fflush($handle);
            flock($handle, \LOCK_UN);
        } finally {
            fclose($handle);
        }
    }
}
