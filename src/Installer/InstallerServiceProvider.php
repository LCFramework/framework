<?php

namespace LCFramework\Framework\Installer;

use Illuminate\Support\ServiceProvider;
use LCFramework\Framework\Installer\Http\Livewire\Installer;
use LCFramework\Framework\LCFramework;
use Livewire\Livewire;

class InstallerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('lcframework::installer', Installer::class);

        if (!LCFramework::installed()) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/installer.php');
        }
    }
}
