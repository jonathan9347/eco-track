<?php

namespace App\Support\Firebase;

class RestFirestoreDocumentSnapshot
{
    public function __construct(
        protected string $id,
        protected array $data,
        protected bool $exists,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function exists(): bool
    {
        return $this->exists;
    }
}
