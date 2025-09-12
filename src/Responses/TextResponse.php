<?php

namespace HosseinHezami\LaravelGemini\Responses;

class TextResponse extends BaseResponse
{
    public function content(): string
    {
        return $this->data['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }
}