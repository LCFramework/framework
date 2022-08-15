<?php

namespace LCFramework\Framework\Admin;

use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use LCFramework\Framework\Admin\Filament\Resources\ModuleResource;
use Spatie\LaravelPackageTools\Package;

class AdminServiceProvider extends PluginServiceProvider
{
    protected array $pages = [
        ModuleResource\Pages\ListModules::class,
    ];

    protected array $resources = [
        ModuleResource::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package->name('lcframework-admin');
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        Filament::serving(function () {
            Filament::registerTheme(
                mix('css/filament.css', 'lcframework'),
            );
        });
    }
}
