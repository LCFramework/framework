<?php

namespace LCFramework\Framework;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class LCFrameworkServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/lcframework.php',
            'lcframework'
        );
    }

    public function boot(): void
    {
        Log::info('LCFramework has booted!');
    }
}
