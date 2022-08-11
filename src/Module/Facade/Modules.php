<?php

namespace LCFramework\Framework\Module\Facade;

use Illuminate\Support\Facades\Facade;

class Modules extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'lcframework.modules';
    }
}
