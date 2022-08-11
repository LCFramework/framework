<?php

namespace LCFramework\Framework;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class LCFrameworkServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Log::info('LCFramework has booted!');
    }
}
