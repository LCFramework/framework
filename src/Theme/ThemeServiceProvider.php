<?php

namespace LCFramework\Framework\Theme;

use Illuminate\Support\ServiceProvider;
use LCFramework\Framework\Theme\Facade\Themes;
use LCFramework\Framework\Theme\Loader\ThemeLoader;
use LCFramework\Framework\Theme\Loader\ThemeLoaderInterface;
use LCFramework\Framework\Theme\Repository\ThemeRepository;
use LCFramework\Framework\Theme\Repository\ThemeRepositoryInterface;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            ThemeLoaderInterface::class,
            ThemeLoader::class
        );

        $this->app->alias(
            ThemeRepositoryInterface::class,
            'lcframework.themes'
        );
        $this->app->singleton(
            ThemeRepositoryInterface::class,
            ThemeRepository::class
        );
    }

    public function boot(): void
    {
        Themes::boot();
    }
}
