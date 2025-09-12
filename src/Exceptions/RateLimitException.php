<?php

namespace HosseinHezami\LaravelGemini\Exceptions;

class RateLimitException extends BaseException
{
    public ?int $retryAfter;

    public function __construct(string $message = 'Rate limit exceeded', ?int $retryAfter = null)
    {
        parent::__construct($message);
        $this->retryAfter = $retryAfter;
    }
}