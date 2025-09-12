<?php

namespace HosseinHezami\LaravelGemini\Exceptions;

class NetworkException extends BaseException
{
    public function __construct(string $message = 'Network issue')
    {
        parent::__construct($message);
    }
}