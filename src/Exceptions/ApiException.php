<?php

namespace HosseinHezami\LaravelGemini\Exceptions;

class ApiException extends BaseException
{
    public function __construct(string $message = 'API error occurred')
    {
        parent::__construct($message);
    }
}