<?php

namespace LCFramework\Framework\Theme\Facade;

use Illuminate\Support\Facades\Facade;

class Themes extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'lcframework.modules';
    }
}
