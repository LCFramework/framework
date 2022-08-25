<?php

namespace LCFramework\Framework\Module\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool enable(string|\LCFramework\Framework\Module\Module $module, ?string &$reason = null)
 * @method static bool delete(string|\LCFramework\Framework\Module\Module $module, ?string &$reason = null)
 * @method static bool install(string $path, ?string &$reason = null)
 *
 * @see \LCFramework\Framework\Module\Repository\ModuleRepository
 */
class Modules extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'lcframework.modules';
    }
}
