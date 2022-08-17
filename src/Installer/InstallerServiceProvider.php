<?php

namespace LCFramework\Framework\Installer;

use Illuminate\Contracts\Encryption\Encrypter as EncrypterInterface;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use LCFramework\Framework\Installer\Http\Livewire\Installer;
use LCFramework\Framework\LCFramework;
use Livewire\Livewire;

class InstallerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->beforeResolving(EncrypterInterface::class, function () {
            if (config()->get('app.key') === null) {
                $key = $this->generateRandomKey();

                if (
                    File::put(
                        base_path('.env'), 'APP_KEY='.$key
                    )
                ) {
                    config()->set('app.key', $key);
                }
            }
        });
    }

    public function boot(): void
    {
        Livewire::component(
            'lcframework::installer',
            Installer::class
        );

        if (! LCFramework::installed()) {
            $this->loadRoutesFrom(
                __DIR__.'/../../routes/installer.php'
            );
        }
    }

    protected function generateRandomKey()
    {
        return 'base64:'.base64_encode(
            Encrypter::generateKey(config('app.cipher'))
        );
    }
}
