<?php

namespace HosseinHezami\LaravelGemini\Builders;

use HosseinHezami\LaravelGemini\Enums\Capability;
use HosseinHezami\LaravelGemini\Responses\TextResponse;

class TextBuilder extends BaseBuilder
{
    protected function getCapability(): string
    {
        return Capability::TEXT->value;
    }

    public function generate(): TextResponse
    {
        return $this->provider->generateText($this->params);
    }
}