<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Firestore;

class EmissionFactorService
{
    public function __construct(
        protected Firestore $firestore,
    ) {
    }

    public function getTransportFactor(string $transportType): float
    {
        return $this->resolveOpenCedaFactor(
            sector: 'transport',
            factorType: 'transport',
            activityKey: $transportType,
            activity: config("emissions.transport_activity_map.{$transportType}", $transportType)
        );
    }

    public function getDietFactor(string $dietType): float
    {
        return $this->resolveOpenCedaFactor(
            sector: 'agriculture',
            factorType: 'diet',
            activityKey: $dietType,
            activity: config("emissions.diet_activity_map.{$dietType}", $dietType)
        );
    }

    public function getElectricityFactor(): float
    {
        $cacheKey = 'emissions:api:climatiq:electricity_factor';
        $fallback = $this->getFallbackFactor('gadgets', 'per_hour');

        try {
            $cached = Cache::get($cacheKey);
            if ($this->isValidCachedFactor($cached)) {
                return (float) $cached['value'];
            }

            $apiKey = (string) config('emissions.apis.climatiq.api_key', '');
            if ($apiKey === '') {
                throw new \RuntimeException('Climatiq API key is not configured.');
            }

            $response = Http::baseUrl((string) config('emissions.apis.climatiq.base_url'))
                ->withToken($apiKey)
                ->acceptJson()
                ->timeout(15)
                ->post((string) config('emissions.apis.climatiq.estimate_path'), [
                    'emission_factor' => config('emissions.apis.climatiq.emission_factor'),
                    'parameters' => [
                        'energy' => 1.0,
                        'energy_unit' => 'kWh',
                    ],
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException('Climatiq request failed with status '.$response->status().'.');
            }

            $value = $this->extractNumericFactor($response->json(), [
                'co2e',
                'data.co2e',
            ]);

            if ($value === null) {
                throw new \RuntimeException('Climatiq response did not include a numeric co2e value.');
            }

            $this->storeCachedFactor($cacheKey, $value, 'api', [
                'provider' => 'climatiq',
                'factor_name' => (string) config('emissions.apis.climatiq.emission_factor'),
            ]);

            return $value;
        } catch (\Throwable $exception) {
            Log::warning('Unable to fetch electricity emission factor from Climatiq.', [
                'error' => $exception->getMessage(),
            ]);

            return $this->fallbackFromCacheOrManual($cacheKey, $fallback);
        }
    }

    public function getDeviceWattage(string $deviceType): float
    {
        return (float) config("emissions.device_wattages.{$deviceType}", config('emissions.device_wattages.laptop', 0.05));
    }

    public function getApiStatusSummary(): array
    {
        $transportStatuses = [];
        foreach (array_keys(config('emissions.fallback.transport', [])) as $transportType) {
            $transportStatuses[$transportType] = $this->describeFactorStatus(
                cacheKey: $this->factorCacheKey('transport', $transportType),
                fallbackValue: $this->getFallbackFactor('transport', $transportType)
            );
        }

        $dietStatuses = [];
        foreach (array_keys(config('emissions.fallback.diet', [])) as $dietType) {
            $dietStatuses[$dietType] = $this->describeFactorStatus(
                cacheKey: $this->factorCacheKey('diet', $dietType),
                fallbackValue: $this->getFallbackFactor('diet', $dietType)
            );
        }

        return [
            'transport' => $transportStatuses,
            'diet' => $dietStatuses,
            'gadgets' => [
                'electricity' => $this->describeFactorStatus(
                    cacheKey: 'emissions:api:climatiq:electricity_factor',
                    fallbackValue: $this->getFallbackFactor('gadgets', 'per_hour')
                ),
            ],
        ];
    }

    protected function resolveOpenCedaFactor(
        string $sector,
        string $factorType,
        string $activityKey,
        string $activity,
    ): float {
        $cacheKey = $this->factorCacheKey($factorType, $activityKey);
        $fallback = $this->getFallbackFactor($factorType, $activityKey);

        try {
            $cached = Cache::get($cacheKey);
            if ($this->isValidCachedFactor($cached)) {
                return (float) $cached['value'];
            }

            $response = Http::baseUrl((string) config('emissions.apis.open_ceda.base_url'))
                ->acceptJson()
                ->timeout(15)
                ->get((string) config('emissions.apis.open_ceda.factors_path'), [
                    'country' => config('emissions.apis.open_ceda.country'),
                    'sector' => $sector,
                    'activity' => $activity,
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException('Open CEDA request failed with status '.$response->status().'.');
            }

            $value = $this->extractNumericFactor($response->json(), [
                'emission_factor',
                'factor',
                'co2e',
                'data.emission_factor',
                'data.factor',
                'data.co2e',
                'data.0.emission_factor',
                'data.0.factor',
                'data.0.co2e',
            ]);

            if ($value === null) {
                throw new \RuntimeException('Open CEDA response did not include a numeric emission factor.');
            }

            $this->storeCachedFactor($cacheKey, $value, 'api', [
                'provider' => 'open_ceda',
                'sector' => $sector,
                'activity' => $activity,
            ]);

            return $value;
        } catch (\Throwable $exception) {
            Log::warning('Unable to fetch emission factor from Open CEDA.', [
                'sector' => $sector,
                'activity_key' => $activityKey,
                'activity' => $activity,
                'error' => $exception->getMessage(),
            ]);

            return $this->fallbackFromCacheOrManual($cacheKey, $fallback);
        }
    }

    protected function fallbackFromCacheOrManual(string $cacheKey, float $fallback): float
    {
        $cached = Cache::get($cacheKey);
        if ($this->isValidCachedFactor($cached)) {
            return (float) $cached['value'];
        }

        $this->storeCachedFactor($cacheKey, $fallback, 'fallback');

        return $fallback;
    }

    protected function storeCachedFactor(string $cacheKey, float $value, string $source, array $meta = []): void
    {
        Cache::put($cacheKey, [
            'value' => $value,
            'source' => $source,
            'last_synced_at' => now()->toIso8601String(),
            ...$meta,
        ], (int) config('emissions.cache_duration', 86400));
    }

    protected function describeFactorStatus(string $cacheKey, float $fallbackValue): array
    {
        $cached = Cache::get($cacheKey);

        return [
            'value' => $this->isValidCachedFactor($cached) ? (float) $cached['value'] : $fallbackValue,
            'source' => $cached['source'] ?? 'fallback',
            'last_synced_at' => $cached['last_synced_at'] ?? null,
        ];
    }

    protected function factorCacheKey(string $factorType, string $activityKey): string
    {
        return sprintf('emissions:api:%s:%s', $factorType, $activityKey);
    }

    protected function getFallbackFactor(string $group, string $key): float
    {
        $firebaseFactors = $this->getFirebaseFallbackFactors();

        return (float) Arr::get(
            $firebaseFactors,
            "{$group}.{$key}",
            config("emissions.fallback.{$group}.{$key}", 0.0)
        );
    }

    protected function getFirebaseFallbackFactors(): array
    {
        return Cache::remember('emissions:fallback:firebase', 300, function (): array {
            try {
                $snapshot = $this->firestore
                    ->database()
                    ->collection('settings')
                    ->document('emission_factors')
                    ->snapshot();

                if (! $snapshot->exists()) {
                    return [];
                }

                return $snapshot->data();
            } catch (\Throwable $exception) {
                Log::warning('Unable to load emission factor fallbacks from Firebase.', [
                    'error' => $exception->getMessage(),
                ]);

                return [];
            }
        });
    }

    protected function extractNumericFactor(mixed $payload, array $paths): ?float
    {
        foreach ($paths as $path) {
            $value = data_get($payload, $path);

            if (is_numeric($value)) {
                return (float) $value;
            }
        }

        return null;
    }

    protected function isValidCachedFactor(mixed $cached): bool
    {
        return is_array($cached) && array_key_exists('value', $cached) && is_numeric($cached['value']);
    }
}
