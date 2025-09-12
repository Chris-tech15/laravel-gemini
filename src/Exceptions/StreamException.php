<?php

namespace HosseinHezami\LaravelGemini\Exceptions;

class StreamException extends BaseException
{
    public function __construct(string $message = 'Streaming interrupted')
    {
        parent::__construct($message);
    }
}