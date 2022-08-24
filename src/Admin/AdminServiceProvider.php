<?php

namespace LCFramework\Framework\Admin;

use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use LCFramework\Framework\Admin\Filament\Pages\SiteSettings;
use LCFramework\Framework\Admin\Filament\Resources\ModuleResource;
use LCFramework\Framework\Admin\Filament\Resources\RoleResource;
use LCFramework\Framework\Admin\Filament\Resources\ThemeResource;
use LCFramework\Framework\Admin\Filament\Resources\UserResource;
use Spatie\LaravelPackageTools\Package;

class AdminServiceProvider extends PluginServiceProvider
{
    protected array $pages = [
        SiteSettings::class,
    ];

    protected array $resources = [
        ModuleResource::class,
        ThemeResource::class,
        UserResource::class,
        RoleResource::class
    ];

    public function configurePackage(Package $package): void
    {
        $package->name('lcframework-admin');
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        Filament::serving(function () {
            Filament::registerNavigationGroups([
                'Users',
                'Appearance',
                'Extend',
                'Administration',
            ]);

            Filament::registerTheme(
                mix('css/filament.css', 'lcframework'),
            );
        });
    }
}
