<?php

namespace HosseinHezami\LaravelGemini\Responses;

class ImageResponse extends BaseResponse
{
    public function content(): string
    {
        return base64_decode($this->data['candidates'][0]['content']['parts'][0]['inlineData']['data']);
    }

    public function url(): string
    {
        return $this->data['uri'] ?? '';
    }

    public function save(string $path): void
    {
        file_put_contents($path, $this->content());
    }
}