<?php

namespace LCFramework\Framework\Setting;

use Illuminate\Support\Manager;
use LCFramework\Framework\Setting\Drivers\SettingsDatabaseDriver;
use LCFramework\Framework\Setting\Drivers\SettingsFileDriver;

class SettingsManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return $this->config->get('lcframework.settings.driver');
    }

    public function getFileDriver(): SettingsFileDriver
    {
        return $this->container->make(SettingsFileDriver::class);
    }

    public function getDatabaseDriver(): SettingsDatabaseDriver
    {
        return $this->container->make(SettingsDatabaseDriver::class);
    }
}
