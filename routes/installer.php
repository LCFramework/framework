<?php

use Illuminate\Support\Facades\Route;
use LCFramework\Framework\Installer\Http\Controllers\InstallerController;

Route::middleware('web')->group(function () {
    Route::get('/install', [InstallerController::class, 'show'])
        ->name('installer');
});
