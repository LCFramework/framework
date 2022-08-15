<?php

use Illuminate\Support\Facades\Route;
use LCFramework\Framework\Auth\Http\Controllers\EmailVerificationController;
use LCFramework\Framework\Auth\Http\Controllers\LoginController;
use LCFramework\Framework\Auth\Http\Controllers\LogoutController;
use LCFramework\Framework\Auth\Http\Controllers\PasswordConfirmationController;
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

    Route::middleware('auth')->group(function () use ($routes, $passwordRoutes) {
        $logoutRoute = $routes['logout'] ?? null;

        $emailRoutes = $routes['email'] ?? [];
        $emailNoticeRoute = $emailRoutes['notice'] ?? null;
        $emailVerifyRoute = $emailRoutes['verify'] ?? null;

        $passwordConfirmationRoute = $passwordRoutes['confirm'];

        if ($logoutRoute !== null) {
            Route::post(
                $logoutRoute,
                [
                    LogoutController::class,
                    'destroy',
                ]
            )->name('logout');
        }

        if ($emailNoticeRoute !== null) {
            Route::get($emailNoticeRoute, [EmailVerificationController::class, 'create'])
                ->name('email-verification.notice');
        }

        if ($emailVerifyRoute !== null) {
            Route::post($emailVerifyRoute, [EmailVerificationController::class, 'store'])
                ->middleware(['signed', 'throttle:5,1'])
                ->name('email-verification.store');
        }

        if ($passwordConfirmationRoute !== null) {
            Route::get(
                $passwordConfirmationRoute,
                [
                    PasswordConfirmationController::class,
                    'create'
                ]
            )->name('password.confirm');
        }
    });
});
