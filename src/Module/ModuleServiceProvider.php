<?php

namespace LCFramework\Framework\Module;

use Illuminate\Support\ServiceProvider;
use LCFramework\Framework\Module\Facade\Modules;
use LCFramework\Framework\Module\Installer\ModuleInstaller;
use LCFramework\Framework\Module\Installer\ModuleInstallerInterface;
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

        $this->app->singleton(
            ModuleInstallerInterface::class,
            ModuleInstaller::class
        );

        $this->app->alias(
            ModuleRepositoryInterface::class,
            'lcframework.modules'
        );
        $this->app->singleton(
            ModuleRepositoryInterface::class,
            ModuleRepository::class
        );

        $this->app->beforeResolving('filament', function() {
            Modules::boot();
        });
    }
}
