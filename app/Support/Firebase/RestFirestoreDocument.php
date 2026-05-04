<?php

namespace App\Support\Firebase;

use RuntimeException;

class RestFirestoreDocument
{
    public function __construct(
        protected RestFirestoreClient $client,
        protected string $path,
    ) {
    }

    public function id(): string
    {
        return basename($this->path);
    }

    public function snapshot(): RestFirestoreDocumentSnapshot
    {
        try {
            $document = $this->client->request('GET', $this->path);

            return new RestFirestoreDocumentSnapshot(
                id: $this->id(),
                data: $this->client->decodeDocument($document),
                exists: true,
            );
        } catch (RuntimeException $exception) {
            if (str_contains($exception->getMessage(), 'status 404')) {
                return new RestFirestoreDocumentSnapshot($this->id(), [], false);
            }

            throw $exception;
        }
    }

    public function set(array $data, array $options = []): void
    {
        $query = [];

        if (($options['merge'] ?? false) === true) {
            $query['updateMask.fieldPaths'] = array_keys($data);
        }

        $this->client->request('PATCH', $this->path, $query, $this->client->encodeDocument($data));
    }

    public function delete(): void
    {
        $this->client->request('DELETE', $this->path);
    }
}
