<?php

namespace HosseinHezami\LaravelGemini\Exceptions;

class AuthenticationException extends BaseException
{
    public function __construct(string $message = 'Invalid API key or authentication issue')
    {
        parent::__construct($message);
    }
}