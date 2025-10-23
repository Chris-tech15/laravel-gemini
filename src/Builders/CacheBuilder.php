<?php

namespace HosseinHezami\LaravelGemini\Builders;

use HosseinHezami\LaravelGemini\Providers\GeminiProvider;
use HosseinHezami\LaravelGemini\Responses\CacheResponse;
use HosseinHezami\LaravelGemini\Exceptions\ValidationException;

class CacheBuilder
{
    protected GeminiProvider $provider;

    public function __construct(GeminiProvider $provider)
    {
        $this->provider = $provider;
    }
    
    // Create a cached content with direct parameters
    public function create(
        string $model,
        array $contents,
        ?string $systemInstruction = null,
        array $tools = [],
        array $toolConfig = [],
        ?string $displayName = null,
        ?string $ttl = null,
        ?string $expireTime = null
    ): CacheResponse {
        if (empty($model) || empty($contents)) {
            throw new ValidationException('Model and contents are required for creating cache.');
        }

        $params = compact('model', 'contents', 'systemInstruction', 'tools', 'toolConfig', 'displayName', 'ttl', 'expireTime');
        return $this->provider->createCachedContent($params);
    }

    // List cached contents with optional params
    public function list(?int $pageSize = null, ?string $pageToken = null): CacheResponse
    {
        $params = array_filter(compact('pageSize', 'pageToken'));
        return $this->provider->listCachedContents($params);
    }

    // Get a cached content by name
    public function get(string $name): CacheResponse
    {
        if (empty($name)) {
            throw new ValidationException('Cache name is required.');
        }
        return $this->provider->getCachedContent($name);
    }

    // Update cache expiration
    public function update(
        string $name,
        ?string $ttl = null,
        ?string $expireTime = null
    ): CacheResponse {
        if (empty($name)) {
            throw new ValidationException('Cache name is required.');
        }
        if (empty($ttl) && empty($expireTime)) {
            throw new ValidationException('TTL or expireTime is required for update.');
        }

        $expiration = array_filter(compact('ttl', 'expireTime'));
        return $this->provider->updateCachedContent($name, $expiration);
    }

    // Delete a cached content by name
    public function delete(string $name): bool
    {
        if (empty($name)) {
            throw new ValidationException('Cache name is required.');
        }
        return $this->provider->deleteCachedContent($name);
    }
}