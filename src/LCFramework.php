<?php

namespace LCFramework\Framework;

use Illuminate\Support\Facades\Storage;

class LCFramework
{
    const VERSION = '0.0.1';

    private static ?bool $installed = null;

    public static function installed(): bool
    {
        if (static::$installed !== null) {
            return static::$installed;
        }

        return static::$installed = Storage::exists('lcframework');
    }
}
