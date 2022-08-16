<?php

namespace LCFramework\Framework\Auth;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Support\Facades\Auth;
use LCFramework\Framework\Auth\Hashing\Drivers\PlainTextHashingDriver;
use LCFramework\Framework\Auth\Hashing\Drivers\Sha256HashingDriver;
use LCFramework\Framework\Auth\Hashing\HashManager;
use LCFramework\Framework\Auth\Http\Livewire\EmailVerification;
use LCFramework\Framework\Auth\Http\Livewire\Login;
use LCFramework\Framework\Auth\Http\Livewire\PasswordConfirmation;
use LCFramework\Framework\Auth\Http\Livewire\PasswordRequest;
use LCFramework\Framework\Auth\Http\Livewire\PasswordReset;
use LCFramework\Framework\Auth\Http\Livewire\Register;
use LCFramework\Framework\Auth\Listeners\SendEmailVerificationNotification;
use LCFramework\Framework\Auth\Models\User;
use Livewire\Livewire;

class AuthServiceProvider extends EventServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    public function register()
    {
        parent::register();

        $this->registerHashing();
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/auth.php');

        $this->registerLivewireComponents();

        Auth::provider('eloquent', function (Application $app): EloquentUserProvider {
            return new EloquentUserProvider(
                $app['hash'],
                User::class
            );
        });
    }

    protected function registerHashing(): void
    {
        $this->app->alias(HashManager::class, 'hash');
        $this->app->singleton(Hasher::class, HashManager::class);
        $this->app->singleton(PlainTextHashingDriver::class);
        $this->app->singleton(Sha256HashingDriver::class);
    }

    protected function registerLivewireComponents(): void
    {
        Livewire::component('lcframework::login', Login::class);
        Livewire::component('lcframework::register', Register::class);
        Livewire::component('lcframework::password-reset', PasswordReset::class);
        Livewire::component('lcframework::password-request', PasswordRequest::class);
        Livewire::component('lcframework::password-confirmation', PasswordConfirmation::class);
        Livewire::component('lcframework::email-verification', EmailVerification::class);
    }
}
