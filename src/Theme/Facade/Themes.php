<?php

namespace LCFramework\Framework\Theme\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array all()
 * @method static null|\LCFramework\Framework\Theme\Theme enabled()
 * @method static array disabled()
 * @method static bool enable(string|\LCFramework\Framework\Theme\Theme $theme, ?string &$reason = null)
 * @method static void disable()
 * @method static null|\LCFramework\Framework\Theme\Theme find()
 * @method static \LCFramework\Framework\Theme\Theme findOrFail()
 * @method static bool validate(string|\LCFramework\Framework\Theme\Theme $theme, ?string &$reason = null)
 * @method static void boot()
 * @method static bool delete(string|\LCFramework\Framework\Theme\Theme $theme, ?string &$reason = null)
 * @method static bool install(string $path, ?string &$reason = null)
 *
 * @see \LCFramework\Framework\Theme\Repository\ThemeRepository
 */
class Themes extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'lcframework.themes';
    }
}
