<?php

namespace LCFramework\Framework\Transformer\Facade;

use Illuminate\Support\Facades\Facade;

class Transformer extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'lcframework.transformer';
    }
}
