<?php

namespace LCFramework\Framework;

use Illuminate\Support\AggregateServiceProvider;
use LCFramework\Framework\Auth\AuthServiceProvider;
use LCFramework\Framework\Module\ModuleServiceProvider;
use LCFramework\Framework\Setting\SettingsServiceProvider;
use LCFramework\Framework\Support\Filesystem;
use LCFramework\Framework\Theme\ThemeServiceProvider;
use LCFramework\Framework\Transformer\TransformerServiceProvider;

class LCFrameworkServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        SettingsServiceProvider::class,
        TransformerServiceProvider::class,
        AuthServiceProvider::class,
        ModuleServiceProvider::class,
        ThemeServiceProvider::class,
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

    public function boot(): void
    {
        $this->loadViewsFrom(
            __DIR__ . '/../resources/views',
            'lcframework'
        );

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../dist' => public_path('lcframework'),
            ], 'assets');
        }
    }
}
