<?php

namespace HosseinHezami\LaravelGemini\Enums;

enum Capability: string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case VIDEO = 'video';
    case AUDIO = 'audio';
}