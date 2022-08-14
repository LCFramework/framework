<?php

use Illuminate\Support\Facades\Route;
use LCFramework\Framework\Auth\Http\Controllers\LoginController;
use LCFramework\Framework\Auth\Http\Controllers\LogoutController;
use LCFramework\Framework\Auth\Http\Controllers\PasswordRequestController;
use LCFramework\Framework\Auth\Http\Controllers\PasswordResetController;
use LCFramework\Framework\Auth\Http\Controllers\RegisterController;

$routes = config('lcframework.auth.routes');
if ($routes === null) {
    return;
}

Route::middleware('web')->group(function () use ($routes) {
    $passwordRoutes = $routes['password'] ?? [];

    Route::middleware('guest')->group(function () use ($routes, $passwordRoutes) {
        $loginRoute = $routes['login'] ?? null;
        $registerRoute = $routes['register'] ?? null;
        $passwordRequestRoute = $passwordRoutes['request'] ?? null;
        $resetPasswordRoute = $passwordRoutes['reset'] ?? null;

        if ($loginRoute !== null) {
            Route::get($loginRoute, [LoginController::class, 'create'])
                ->name('login');
        }

        if ($registerRoute !== null) {
            Route::get($registerRoute, [RegisterController::class, 'create'])
                ->name('register');
        }

        if ($passwordRequestRoute !== null) {
            Route::get($passwordRequestRoute, [PasswordRequestController::class, 'create'])
                ->name('password.request');
        }

        if ($resetPasswordRoute !== null) {
            Route::get(
                $resetPasswordRoute . '/{token}',
                [PasswordResetController::class, 'create']
            )->name('password.reset');
        }
    });

    Route::middleware('auth')->group(function () use ($routes) {
        $logoutRoute = $routes['logout'] ?? null;

        if ($logoutRoute !== null) {
            Route::post(
                $logoutRoute,
                [
                    LogoutController::class,
                    'destroy',
                ]
            )->name('logout');
        }
    });
});
