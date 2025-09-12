<?php

namespace HosseinHezami\LaravelGemini\Factory;

use HosseinHezami\LaravelGemini\Contracts\ProviderInterface;
use HosseinHezami\LaravelGemini\Exceptions\ValidationException;

class ProviderFactory
{
    public function create(?string $alias = null): ProviderInterface
    {
        $alias = $alias ?: config('gemini.default_provider');

        $providerConfig = config('gemini.providers.' . $alias);

        if (!$providerConfig || !isset($providerConfig['class'])) {
            throw new ValidationException("Unknown provider: $alias");
        }

        $class = $providerConfig['class'];

        return new $class($providerConfig);
    }
}
