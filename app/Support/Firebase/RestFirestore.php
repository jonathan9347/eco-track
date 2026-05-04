<?php

namespace App\Support\Firebase;

use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Contract\Firestore;
use RuntimeException;

class RestFirestore implements Firestore
{
    public function __construct(
        protected array $credentials,
        protected ?string $database = null,
    ) {
    }

    public static function fromFirebaseConfig(array $config): self
    {
        $credentials = self::resolveCredentials($config['credentials'] ?? null);

        return new self(
            credentials: $credentials,
            database: $config['firestore']['database'] ?? null,
        );
    }

    public function database(): FirestoreClient
    {
        return new RestFirestoreClient(
            credentials: $this->credentials,
            database: $this->database ?: FirestoreClient::DEFAULT_DATABASE,
        );
    }

    protected static function resolveCredentials(mixed $configuredCredentials): array
    {
        if (is_string($configuredCredentials) && $configuredCredentials !== '' && is_file($configuredCredentials)) {
            $json = file_get_contents($configuredCredentials);
            $credentials = json_decode((string) $json, true);

            if (is_array($credentials)) {
                return $credentials;
            }
        }

        foreach (['FIREBASE_CREDENTIALS_JSON', 'GOOGLE_APPLICATION_CREDENTIALS_JSON', 'FIREBASE_CREDENTIALS'] as $key) {
            $value = getenv($key);

            if (! is_string($value) || trim($value) === '' || ! str_starts_with(trim($value), '{')) {
                continue;
            }

            $credentials = json_decode($value, true);

            if (is_array($credentials)) {
                return $credentials;
            }
        }

        throw new RuntimeException('Firebase service account credentials were not found.');
    }
}
