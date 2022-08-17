<?php

namespace LCFramework\Framework\Installer;

use Illuminate\Contracts\Encryption\Encrypter as EncrypterInterface;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\ServiceProvider;
use LCFramework\Framework\Installer\Http\Livewire\Installer;
use LCFramework\Framework\LCFramework;
use Livewire\Livewire;

class InstallerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->beforeResolving(EncrypterInterface::class, function ($app) {
            if ($app['config']->get('app.key') === null) {
                $app['config']->set('app.key', $this->generateRandomKey());
            }
        });
    }

    public function boot(): void
    {
        Livewire::component(
            'lcframework::installer',
            Installer::class
        );

        if (!LCFramework::installed()) {
            $this->loadRoutesFrom(
                __DIR__ . '/../../routes/installer.php'
            );
        }
    }

    protected function generateRandomKey()
    {
        return 'base64:' . base64_encode(
                Encrypter::generateKey(config('app.cipher'))
            );
    }
}
