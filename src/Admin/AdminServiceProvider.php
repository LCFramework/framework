<?php

namespace LCFramework\Framework\Admin;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
use LCFramework\Framework\Admin\Filament\Resources\ModuleResource;

class AdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerFilament();
    }

    public function boot(): void
    {
        Filament::serving(function () {
            Filament::registerTheme(
                mix('css/filament.css', 'lcframework'),
            );
        });
    }

    protected function registerFilament(): void
    {
        $this->app->resolving('filament', function () {
            Filament::registerResources([
                ModuleResource::class,
            ]);
        });
    }
}
