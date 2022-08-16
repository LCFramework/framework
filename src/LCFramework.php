<?php

namespace LCFramework\Framework;

use Illuminate\Support\Facades\Storage;

class LCFramework
{
    const VERSION = '0.0.1';

    public static function installed(): bool
    {
        return Storage::exists('lcframework');
    }
}
