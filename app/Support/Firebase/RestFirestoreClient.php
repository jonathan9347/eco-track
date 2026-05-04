<?php

namespace App\Support\Firebase;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RestFirestoreClient extends FirestoreClient
{
    protected ?string $accessToken = null;

    protected ?int $accessTokenExpiresAt = null;

    public function __construct(
        protected array $credentials,
        protected string $database = FirestoreClient::DEFAULT_DATABASE,
    ) {
    }

    public function collection($name)
    {
        return new RestFirestoreCollection($this, trim($name, '/'));
    }

    public function document($name)
    {
        return new RestFirestoreDocument($this, trim($name, '/'));
    }

    public function request(string $method, string $path, array $query = [], array $body = []): array
    {
        $url = $this->url($path);

        if ($query !== []) {
            $url .= '?'.$this->queryString($query);
        }

        $response = Http::withToken($this->accessToken())
            ->acceptJson()
            ->asJson()
            ->timeout(20)
            ->send($method, $url, [
                'json' => $body,
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Firestore REST request failed with status '.$response->status().': '.$response->body());
        }

        return $response->json() ?? [];
    }

    public function projectId(): string
    {
        $projectId = $this->credentials['project_id'] ?? getenv('FIREBASE_PROJECT_ID') ?: getenv('GOOGLE_CLOUD_PROJECT');

        if (! is_string($projectId) || $projectId === '') {
            throw new RuntimeException('Firebase project ID was not found in service account credentials.');
        }

        return $projectId;
    }

    public function databaseId(): string
    {
        return $this->database;
    }

    public function encodeDocument(array $data): array
    {
        return [
            'fields' => collect($data)
                ->map(fn (mixed $value): array => $this->encodeFieldValue($value))
                ->all(),
        ];
    }

    public function encodeFieldValue(mixed $value): array
    {
        return $this->encodeValue($value);
    }

    public function decodeDocument(array $document): array
    {
        return collect($document['fields'] ?? [])
            ->map(fn (array $value): mixed => $this->decodeValue($value))
            ->all();
    }

    protected function accessToken(): string
    {
        if ($this->accessToken && $this->accessTokenExpiresAt && $this->accessTokenExpiresAt > time() + 60) {
            return $this->accessToken;
        }

        $fetcher = new ServiceAccountCredentials(
            FirestoreClient::FULL_CONTROL_SCOPE,
            $this->credentials,
        );

        $token = $fetcher->fetchAuthToken();

        if (! is_string($token['access_token'] ?? null)) {
            throw new RuntimeException('Unable to fetch a Firebase access token.');
        }

        $this->accessToken = $token['access_token'];
        $this->accessTokenExpiresAt = time() + (int) ($token['expires_in'] ?? 3600);

        return $this->accessToken;
    }

    protected function url(string $path): string
    {
        $documentsPath = str_starts_with($path, ':')
            ? 'documents'.$path
            : 'documents/'.ltrim($path, '/');

        return sprintf(
            'https://firestore.googleapis.com/v1/projects/%s/databases/%s/%s',
            rawurlencode($this->projectId()),
            rawurlencode($this->databaseId()),
            $documentsPath,
        );
    }

    protected function queryString(array $query): string
    {
        $parts = [];

        foreach ($query as $key => $value) {
            foreach ((array) $value as $item) {
                $parts[] = rawurlencode((string) $key).'='.rawurlencode((string) $item);
            }
        }

        return implode('&', $parts);
    }

    protected function encodeValue(mixed $value): array
    {
        if ($value === null) {
            return ['nullValue' => null];
        }

        if (is_bool($value)) {
            return ['booleanValue' => $value];
        }

        if (is_int($value)) {
            return ['integerValue' => (string) $value];
        }

        if (is_float($value)) {
            return ['doubleValue' => $value];
        }

        if (is_array($value)) {
            if (array_is_list($value)) {
                return [
                    'arrayValue' => [
                        'values' => array_map(fn (mixed $item): array => $this->encodeValue($item), $value),
                    ],
                ];
            }

            return [
                'mapValue' => [
                    'fields' => collect($value)
                        ->map(fn (mixed $item): array => $this->encodeValue($item))
                        ->all(),
                ],
            ];
        }

        return ['stringValue' => (string) $value];
    }

    protected function decodeValue(array $value): mixed
    {
        return match (true) {
            array_key_exists('nullValue', $value) => null,
            array_key_exists('booleanValue', $value) => (bool) $value['booleanValue'],
            array_key_exists('integerValue', $value) => (int) $value['integerValue'],
            array_key_exists('doubleValue', $value) => (float) $value['doubleValue'],
            array_key_exists('timestampValue', $value) => (string) $value['timestampValue'],
            array_key_exists('arrayValue', $value) => array_map(
                fn (array $item): mixed => $this->decodeValue($item),
                $value['arrayValue']['values'] ?? [],
            ),
            array_key_exists('mapValue', $value) => collect($value['mapValue']['fields'] ?? [])
                ->map(fn (array $item): mixed => $this->decodeValue($item))
                ->all(),
            default => (string) ($value['stringValue'] ?? ''),
        };
    }
}
