<?php

namespace LCFramework\Framework\Module;

use Illuminate\Support\ServiceProvider;
use LCFramework\Framework\Module\Facade\Modules;
use LCFramework\Framework\Module\Loader\ModuleLoader;
use LCFramework\Framework\Module\Loader\ModuleLoaderInterface;
use LCFramework\Framework\Module\Repository\ModuleRepository;
use LCFramework\Framework\Module\Repository\ModuleRepositoryInterface;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            ModuleLoaderInterface::class,
            ModuleLoader::class
        );

        $this->app->alias(
            ModuleRepositoryInterface::class,
            'lcframework.modules'
        );
        $this->app->singleton(
            ModuleRepositoryInterface::class,
            ModuleRepository::class
        );
    }

    public function boot(): void
    {
        Modules::boot();
    }
}
