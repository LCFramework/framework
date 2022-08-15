<?php

namespace LCFramework\Framework\Admin;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Filament::serving(function () {
            Filament::registerTheme(
                mix('css/filament.css', 'lcframework'),
            );
        });
    }
}
