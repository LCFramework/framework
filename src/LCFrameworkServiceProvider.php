<?php

namespace LCFramework\Framework;

use Illuminate\Support\AggregateServiceProvider;
use Illuminate\Support\Facades\Log;
use LCFramework\Framework\Module\ModuleServiceProvider;
use LCFramework\Framework\Setting\SettingsServiceProvider;
use LCFramework\Framework\Support\Filesystem;
use LCFramework\Framework\Theme\ThemeServiceProvider;

class LCFrameworkServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        SettingsServiceProvider::class,
        ModuleServiceProvider::class,
        ThemeServiceProvider::class
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/lcframework.php',
            'lcframework'
        );

        $this->app->alias(Filesystem::class, 'files');
        $this->app->singleton(Filesystem::class);

        parent::register();
    }
}
