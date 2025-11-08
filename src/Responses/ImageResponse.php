<?php

namespace HosseinHezami\LaravelGemini\Responses;

use HosseinHezami\LaravelGemini\Exceptions\ApiException;

class ImageResponse extends BaseResponse
{
    public function content(): string
    {
        foreach($this->data['candidates'][0]['content']['parts'] as $parts){
            if(key_exists('inlineData', $parts))
                $part = $parts;
        }
        if(!isset($part))
            throw new ApiException("Failed to retrieve image content. No inlineData found.");
        return base64_decode($part['inlineData']['data']);
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
