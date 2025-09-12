<?php

namespace HosseinHezami\LaravelGemini\Exceptions;

class ValidationException extends BaseException
{
    public function __construct(string $message = 'Invalid input parameters')
    {
        parent::__construct($message);
    }
}