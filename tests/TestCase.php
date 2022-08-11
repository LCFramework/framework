<?php

namespace LCFramework\Framework\Tests;

use LCFramework\Framework\LCFrameworkServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LCFrameworkServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }
}