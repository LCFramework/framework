<?php

namespace LCFramework\Framework\Auth;

use Illuminate\Support\ServiceProvider;
use LCFramework\Framework\Auth\Http\Livewire\Login;
use Livewire\Livewire;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/auth.php');

        Livewire::component('lcframework::login', Login::class);
    }
}
