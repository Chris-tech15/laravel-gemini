<?php

namespace HosseinHezami\LaravelGemini\Builders;

use HosseinHezami\LaravelGemini\Enums\Capability;
use HosseinHezami\LaravelGemini\Responses\VideoResponse;

class VideoBuilder extends BaseBuilder
{
    protected function getCapability(): string
    {
        return Capability::VIDEO->value;
    }

    public function generate(): VideoResponse
    {
        return $this->provider->generateVideo($this->params);
    }
}