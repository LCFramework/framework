<?php

namespace LCFramework\Framework\Auth;

use Illuminate\Support\ServiceProvider;
use LCFramework\Framework\Auth\Http\Livewire\EmailVerification;
use LCFramework\Framework\Auth\Http\Livewire\Login;
use LCFramework\Framework\Auth\Http\Livewire\PasswordRequest;
use LCFramework\Framework\Auth\Http\Livewire\PasswordReset;
use LCFramework\Framework\Auth\Http\Livewire\Register;
use Livewire\Livewire;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/auth.php');

        Livewire::component('lcframework::login', Login::class);
        Livewire::component('lcframework::register', Register::class);
        Livewire::component('lcframework::password-reset', PasswordReset::class);
        Livewire::component('lcframework::password-request', PasswordRequest::class);
        Livewire::component('lcframework::email-verification', EmailVerification::class);
    }
}
