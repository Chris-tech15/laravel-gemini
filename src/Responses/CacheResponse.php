<?php

namespace HosseinHezami\LaravelGemini\Responses;

class CacheResponse
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

    public function name(): string
    {
        return $this->data['name'] ?? '';
    }

    public function model(): string
    {
        return $this->data['model'] ?? '';
    }

    public function createTime(): string
    {
        return $this->data['createTime'] ?? '';
    }

    public function updateTime(): string
    {
        return $this->data['updateTime'] ?? '';
    }

    public function expireTime(): string
    {
        return $this->data['expireTime'] ?? '';
    }

    public function displayName(): string
    {
        return $this->data['displayName'] ?? '';
    }

    public function usageMetadata(): array
    {
        return $this->data['usageMetadata'] ?? [];
    }
}