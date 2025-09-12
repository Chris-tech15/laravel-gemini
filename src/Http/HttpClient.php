<?php

namespace HosseinHezami\LaravelGemini\Http;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use HosseinHezami\LaravelGemini\Exceptions\RateLimitException;

class HttpClient
{
    protected PendingRequest $client;

    public function __construct()
    {
        $this->client = Http::baseUrl(config('gemini.base_uri'))
            ->withHeaders(['x-goog-api-key' => config('gemini.api_key')])
            ->timeout(config('gemini.timeout'))
            ->retry(config('gemini.retry_policy.max_retries'), config('gemini.retry_policy.retry_delay'), function ($exception, $request) {
                if ($exception instanceof RateLimitException) {
                    sleep($exception->retryAfter ?? 1);
                    return true;
                }
                return $exception->response->status() >= 500;
            });
    }

    public function post(string $url, array $data): \Illuminate\Http\Client\Response
    {
        return $this->client->post($url, $data);
    }

    public function get(string $url): \Illuminate\Http\Client\Response
    {
        return $this->client->get($url);
    }
    
    public function withOptions(array $options): self
    {
        $this->client = $this->client->withOptions($options);
        return $this;
    }
}