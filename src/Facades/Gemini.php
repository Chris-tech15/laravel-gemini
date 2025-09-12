<?php

namespace HosseinHezami\LaravelGemini\Facades;

use Illuminate\Support\Facades\Facade;
use HosseinHezami\LaravelGemini\Builders\TextBuilder;
use HosseinHezami\LaravelGemini\Builders\ImageBuilder;
use HosseinHezami\LaravelGemini\Builders\VideoBuilder;
use HosseinHezami\LaravelGemini\Builders\AudioBuilder;
use HosseinHezami\LaravelGemini\Builders\FileBuilder;

class Gemini extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'gemini';
    }
	
	public static function text(): TextBuilder
    {
        return static::getFacadeRoot()->text();
    }

    public static function image(): ImageBuilder
    {
        return static::getFacadeRoot()->image();
    }

    public static function video(): VideoBuilder
    {
        return static::getFacadeRoot()->video();
    }

    public static function audio(): AudioBuilder
    {
        return static::getFacadeRoot()->audio();
    }

    public static function files(): FileBuilder
    {
        return static::getFacadeRoot()->files();
    }
}