<?php

namespace HosseinHezami\LaravelGemini\Responses;

class VideoResponse extends BaseResponse
{
    public function content(): string
    {
        return $this->data['video'] ?? '';
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