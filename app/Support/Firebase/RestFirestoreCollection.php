<?php

namespace App\Support\Firebase;

class RestFirestoreCollection
{
    protected array $filters = [];

    public function __construct(
        protected RestFirestoreClient $client,
        protected string $path,
    ) {
    }

    public function document(string $id): RestFirestoreDocument
    {
        return new RestFirestoreDocument($this->client, $this->path.'/'.trim($id, '/'));
    }

    public function add(array $data): RestFirestoreDocument
    {
        $document = $this->client->request('POST', $this->path, body: $this->client->encodeDocument($data));

        return new RestFirestoreDocument($this->client, $this->relativeName($document['name'] ?? ''));
    }

    public function documents(): array
    {
        $response = $this->filters === []
            ? $this->client->request('GET', $this->path, ['pageSize' => 500])
            : $this->client->request('POST', ':runQuery', body: $this->queryPayload());

        $documents = $this->filters === []
            ? ($response['documents'] ?? [])
            : collect($response)->pluck('document')->filter()->values()->all();

        return collect($documents)
            ->map(fn (array $document): RestFirestoreDocumentSnapshot => new RestFirestoreDocumentSnapshot(
                id: basename((string) ($document['name'] ?? '')),
                data: $this->client->decodeDocument($document),
                exists: true,
            ))
            ->all();
    }

    public function where(string $field, string $operator, mixed $value): self
    {
        $clone = clone $this;
        $clone->filters[] = [$field, $operator, $value];

        return $clone;
    }

    protected function queryPayload(): array
    {
        $filters = array_map(fn (array $filter): array => [
            'fieldFilter' => [
                'field' => ['fieldPath' => $filter[0]],
                'op' => $this->operator($filter[1]),
                'value' => $this->client->encodeFieldValue($filter[2]),
            ],
        ], $this->filters);

        return [
            'structuredQuery' => [
                'from' => [
                    ['collectionId' => basename($this->path)],
                ],
                'where' => count($filters) === 1
                    ? $filters[0]
                    : [
                        'compositeFilter' => [
                            'op' => 'AND',
                            'filters' => $filters,
                        ],
                    ],
            ],
        ];
    }

    protected function operator(string $operator): string
    {
        return match ($operator) {
            '==', '=' => 'EQUAL',
            '!=', '<>' => 'NOT_EQUAL',
            '<' => 'LESS_THAN',
            '<=' => 'LESS_THAN_OR_EQUAL',
            '>' => 'GREATER_THAN',
            '>=' => 'GREATER_THAN_OR_EQUAL',
            default => 'EQUAL',
        };
    }

    protected function relativeName(string $name): string
    {
        $needle = '/documents/';
        $position = strpos($name, $needle);

        return $position === false ? $name : substr($name, $position + strlen($needle));
    }
}
