<?php

namespace HosseinHezami\LaravelGemini\Responses;

use HosseinHezami\LaravelGemini\Exceptions\ApiException;

class FileResponse
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function files(): array
    {
        return $this->data['files'] ?? [];
    }

    public function name(): ?string
    {
        return $this->data['name'] ?? null;
    }

    public function uri(): ?string
    {
        return $this->data['uri'] ?? null;
    }

    public function state(): ?string
    {
        return $this->data['state'] ?? null;
    }

    public function mimeType(): ?string
    {
        return $this->data['mimeType'] ?? null;
    }

    public function displayName(): ?string
    {
        return $this->data['displayName'] ?? null;
    }
}