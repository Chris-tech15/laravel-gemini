<?php

namespace HosseinHezami\LaravelGemini\Responses;

abstract class BaseResponse
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    abstract public function content(): string;

    public function toArray(): array
    {
        return $this->data;
    }

    public function json(): string
    {
        return json_encode($this->data);
    }

    public function model(): string
    {
        return $this->data['modelVersion'] ?? '';
    }

    public function usage(): array
    {
        return $this->data['usageMetadata'] ?? [];
    }

    public function requestId(): string
    {
        return $this->data['responseId'] ?? '';
    }
}