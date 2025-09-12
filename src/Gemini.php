<?php

namespace HosseinHezami\LaravelGemini;

use HosseinHezami\LaravelGemini\Builders\TextBuilder;
use HosseinHezami\LaravelGemini\Builders\ImageBuilder;
use HosseinHezami\LaravelGemini\Builders\VideoBuilder;
use HosseinHezami\LaravelGemini\Builders\AudioBuilder;
use HosseinHezami\LaravelGemini\Builders\FileBuilder;
use HosseinHezami\LaravelGemini\Factory\ProviderFactory;

class Gemini
{
    protected ProviderFactory $factory;

    public function __construct(ProviderFactory $factory)
    {
        $this->factory = $factory;
    }

    public function text(): TextBuilder
    {
        return new TextBuilder($this->getProvider());
    }

    public function image(): ImageBuilder
    {
        return new ImageBuilder($this->getProvider());
    }

    public function video(): VideoBuilder
    {
        return new VideoBuilder($this->getProvider());
    }

    public function audio(): AudioBuilder
    {
        return new AudioBuilder($this->getProvider());
    }

    public function files(): FileBuilder
    {
        return new FileBuilder($this->getProvider());
    }

    public function models()
    {
        return $this->getProvider()->models();
    }

    public function embeddings(array $params): array
    {
        return $this->getProvider()->embeddings($params);
    }

    public function getProvider(?string $alias = null)
    {
        return $this->factory->create($alias);
    }
}
