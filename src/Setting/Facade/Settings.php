<?php

namespace LCFramework\Framework\Setting\Facade;

use Illuminate\Support\Facades\Facade;

class Settings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'lc.settings';
    }
}
