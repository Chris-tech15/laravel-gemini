<?php

namespace HosseinHezami\LaravelGemini\Http;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use HosseinHezami\LaravelGemini\Exceptions\RateLimitException;

class HttpClient
{
    protected PendingRequest $client;

    public function __construct(?string $baseUrl = null, ?string $apiKey = null)
    {
        $baseUrl = $baseUrl ?? config('gemini.base_uri');
        $apiKey = $apiKey ?? config('gemini.api_key');
        
        $this->client = Http::baseUrl($baseUrl)
            ->withHeaders(['x-goog-api-key' => $apiKey])
            ->timeout(config('gemini.timeout'))
            ->retry(config('gemini.retry_policy.max_retries'), config('gemini.retry_policy.retry_delay'), function ($exception, $request) {
                if ($exception instanceof RateLimitException) {
                    sleep($exception->retryAfter ?? 1);
                    return true;
                }
                return $exception->response->status() >= 500;
            });
    }

    public function withHeaders(array $headers): self
    {
        $this->client = $this->client->withHeaders($headers);
        return $this;
    }

    public function withBody($content, string $contentType = 'application/json'): self
    {
        $this->client = $this->client->withBody($content, $contentType);
        return $this;
    }

    public function withOptions(array $options): self
    {
        $this->client = $this->client->withOptions($options);
        return $this;
    }

    public function post(string $url, array $data = []): \Illuminate\Http\Client\Response
    {
        return $this->client->post($url, $data);
    }

    public function get(string $url): \Illuminate\Http\Client\Response
    {
        return $this->client->get($url);
    }
    
    public function patch(string $url, array $data): \Illuminate\Http\Client\Response
    {
        return $this->client->patch($url, $data);
    }
    
    public function delete(string $url): \Illuminate\Http\Client\Response
    {
        return $this->client->delete($url);
    }
}