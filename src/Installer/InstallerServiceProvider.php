<?php

namespace LCFramework\Framework\Installer;

use Illuminate\Support\ServiceProvider;
use LCFramework\Framework\LCFramework;

class InstallerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! LCFramework::installed()) {
            $this->loadRoutesFrom(__DIR__.'/../routes/installer.php');
        }
    }
}
