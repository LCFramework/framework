<?php

namespace LCFramework\Framework;

use Illuminate\Support\AggregateServiceProvider;
use Illuminate\Support\Facades\Log;
use LCFramework\Framework\Setting\SettingsServiceProvider;

class LCFrameworkServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        SettingsServiceProvider::class,
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/lcframework.php',
            'lcframework'
        );

        parent::register();
    }

    public function boot(): void
    {
        Log::info('LCFramework has booted!');
    }
}
