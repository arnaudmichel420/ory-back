<?php

declare(strict_types=1);

namespace App\Service\MetierAttractivite;

use App\Enum\MetierAttractiviteCodeEnum;
use App\Service\OAuthApiClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final readonly class MetierAttractiviteApiService
{
    private const ENDPOINT = 'https://api.francetravail.io/partenaire/stats-offres-demandes-emploi/v1/indicateur/stat-perspective-employeur';
    private const SCOPE = 'offresetdemandesemploi api_stats-offres-demandes-emploiv1';

    public function __construct(
        private OAuthApiClient $oAuthApiClient,
        private MetierAttractiviteApiRateLimiter $rateLimiter,
    ) {
    }

    /**
     * @return array{values: array<string, int>, ignored: int}
     */
    public function fetchSnapshot(string $codeDepartement, string $codeRome): array
    {
        $payload = [
            'codeTypeTerritoire' => 'DEP',
            'codeTerritoire' => $codeDepartement,
            'codeTypeActivite' => 'ROME',
            'codeActivite' => $codeRome,
            'codeTypePeriode' => 'ANNEE',
            'codeTypeNomenclature' => 'TYPE_TENSION',
            'dernierePeriode' => true,
            'sansCaracteristiques' => true,
        ];

        try {
            $this->rateLimiter->throttle();
            $response = $this->oAuthApiClient->request('POST', self::ENDPOINT, ['json' => $payload], self::SCOPE);
        } catch (TransportExceptionInterface $exception) {
            throw new MetierAttractiviteApiRetryableException(\sprintf('Erreur reseau sur l\'appel attractivite pour %s/%s.', $codeDepartement, $codeRome), previous: $exception);
        }

        $statusCode = $response->getStatusCode();
        if (429 === $statusCode || $statusCode >= 500) {
            throw new MetierAttractiviteApiRetryableException(\sprintf('L\'API attractivite a repondu avec le statut %d pour %s/%s.', $statusCode, $codeDepartement, $codeRome), $this->extractRetryAfterMilliseconds($response));
        }

        if ($statusCode >= 400) {
            throw new MetierAttractiviteApiException(\sprintf('L\'API attractivite a repondu avec le statut %d pour %s/%s.', $statusCode, $codeDepartement, $codeRome));
        }

        /** @var array<string, mixed> $body */
        $body = $response->toArray(false);

        $rows = $body['listeValeursParPeriode'] ?? [];
        if (!\is_array($rows)) {
            throw new MetierAttractiviteApiException('La reponse attractivite ne contient pas de liste de valeurs exploitable.');
        }

        $values = [];
        $ignored = 0;

        foreach ($rows as $row) {
            if (!\is_array($row)) {
                ++$ignored;
                continue;
            }

            if (($row['codeTypeTerritoire'] ?? null) !== 'DEP' || ($row['codeTypeActivite'] ?? null) !== 'ROME') {
                ++$ignored;
                continue;
            }

            $codeNomenclature = $row['codeNomenclature'] ?? null;
            if (!\is_string($codeNomenclature) || !\in_array($codeNomenclature, MetierAttractiviteCodeEnum::values(), true)) {
                ++$ignored;
                continue;
            }

            $value = $row['valeurPrincipaleNombre'] ?? null;
            if (!\is_int($value) && !\is_string($value) && !\is_float($value)) {
                ++$ignored;
                continue;
            }

            $values[$codeNomenclature] = (int) $value;
        }

        return [
            'values' => $values,
            'ignored' => $ignored,
        ];
    }

    private function extractRetryAfterMilliseconds(ResponseInterface $response): ?int
    {
        $headers = $response->getHeaders(false);
        $values = $headers['retry-after'] ?? null;
        if (!\is_array($values) || [] === $values) {
            return null;
        }

        $retryAfter = trim((string) $values[0]);
        if ('' === $retryAfter) {
            return null;
        }

        if (ctype_digit($retryAfter)) {
            return ((int) $retryAfter) * 1000;
        }

        try {
            $retryAt = new \DateTimeImmutable($retryAfter);
        } catch (\Exception) {
            return null;
        }

        return max(0, ($retryAt->getTimestamp() - time()) * 1000);
    }
}
