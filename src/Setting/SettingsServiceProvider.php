<?php

namespace LCFramework\Framework\Setting;

use Illuminate\Support\ServiceProvider;
use LCFramework\Framework\Setting\Console\Commands\SettingsTableCommand;
use LCFramework\Framework\Setting\Drivers\SettingsDatabaseDriver;
use LCFramework\Framework\Setting\Drivers\SettingsFileDriver;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerManager();
        $this->registerDrivers();
        $this->registerSettingsTableCommand();
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SettingsTableCommand::class,
            ]);
        }
    }

    protected function registerManager(): void
    {
        $this->app->alias(SettingsManager::class, 'lcframework.settings');
        $this->app->singleton(SettingsManager::class);

        $this->app->terminating(function () {
            $this->app->make('lcframework.settings')->save();
        });
    }

    protected function registerDrivers(): void
    {
        $this->app->singleton(SettingsFileDriver::class);
        $this->app->singleton(SettingsDatabaseDriver::class);
    }

    protected function registerSettingsTableCommand(): void
    {
        $this->app->singleton(SettingsTableCommand::class, function ($app) {
            return new SettingsTableCommand($app['files'], $app['composer']);
        });
    }
}
