<?php

namespace LCFramework\Framework\Module\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array all()
 * @method static array ordered()
 * @method static array enabled()
 * @method static array disabled()
 * @method static array status(string $status)
 * @method static bool enable(string|\LCFramework\Framework\Module\Module $module, ?string &$reason = null)
 * @method static void disable(string|\LCFramework\Framework\Module\Module $module)
 * @method static void setStatus(string|\LCFramework\Framework\Module\Module $module)
 * @method static null|\LCFramework\Framework\Module\Module find(string $name)
 * @method static \LCFramework\Framework\Module\Module findOrFail(string $name)
 * @method static bool validate(string|null|\LCFramework\Framework\Module\Module $module, ?string &$reason = null)
 * @method static void boot()
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
