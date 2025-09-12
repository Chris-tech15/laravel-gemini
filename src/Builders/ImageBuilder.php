<?php

namespace HosseinHezami\LaravelGemini\Builders;

use HosseinHezami\LaravelGemini\Enums\Capability;
use HosseinHezami\LaravelGemini\Responses\ImageResponse;

class ImageBuilder extends BaseBuilder
{
    protected function getCapability(): string
    {
        return Capability::IMAGE->value;
    }

    public function generate(): ImageResponse
    {
        return $this->provider->generateImage($this->params);
    }
}