<?php

namespace LCFramework\Framework\Theme\Repository;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\ProviderRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LCFramework\Framework\Module\Repository\ModuleRepositoryInterface;
use LCFramework\Framework\Theme\Exception\InvalidThemeException;
use LCFramework\Framework\Theme\Exception\ThemeNotFoundException;
use LCFramework\Framework\Theme\Installer\ThemeInstallerInterface;
use LCFramework\Framework\Theme\Loader\ThemeLoaderInterface;
use LCFramework\Framework\Theme\Theme;

class ThemeRepository implements ThemeRepositoryInterface
{
    protected Application $app;

    protected ThemeLoaderInterface $loader;

    protected ThemeInstallerInterface $installer;

    protected ModuleRepositoryInterface $modules;

    protected ?array $themes = null;

    protected ?Theme $enabledTheme = null;

    public function __construct(
        Application $app,
        ThemeLoaderInterface $loader,
        ThemeInstallerInterface $installer,
        ModuleRepositoryInterface $modules
    ) {
        $this->app = $app;
        $this->loader = $loader;
        $this->installer = $installer;
        $this->modules = $modules;
    }

    public function all(): array
    {
        if ($this->themes !== null) {
            return $this->themes;
        }

        if (! $this->loadCache()) {
            $this->load();
        }

        return $this->themes;
    }

    public function enabled(): ?Theme
    {
        return $this->enabledTheme;
    }

    public function disabled(): array
    {
        $enabledTheme = $this->enabled();
        if ($enabledTheme === null) {
            return $this->all();
        }

        return collect($this->all())
            ->filter(fn (Theme $theme): bool => $theme->getName() !== $enabledTheme->getName())
            ->all();
    }

    public function enable(Theme|string $theme): void
    {
        if (! ($theme instanceof Theme)) {
            $theme = $this->find($theme);
        }

        if (! $this->validate($theme)) {
            throw InvalidThemeException::theme($theme);
        }

        settings_put('lcframework.themes.enabled', $theme->getName());

        $this->clearCache();

        $this->boot();
    }

    public function disable(): void
    {
        if ($this->enabledTheme === null) {
            return;
        }

        $this->enabledTheme = null;

        settings_forget('lcframework.themes.enabled');

        $this->clearCache();
    }

    public function find(string $name): ?Theme
    {
        return $this->all()[$name] ?? null;
    }

    public function findOrFail(string $name): Theme
    {
        $theme = $this->find($name);
        if ($theme === null) {
            throw ThemeNotFoundException::theme($name);
        }

        return $theme;
    }

    public function validate(Theme|string $theme): bool
    {
        if (! ($theme instanceof Theme)) {
            $theme = $this->find($theme);
        }

        if ($theme === null) {
            return false;
        }

        foreach ($theme->getDependencies() as $dependency => $version) {
            if (! ($dependencyModule = $this->modules->find($dependency))) {
                return false;
            }

            $dependencyVersion = $dependencyModule->getVersion();

            if (
                $dependencyVersion !== '*' &&
                version_compare($dependencyVersion, $version, '<')
            ) {
                return false;
            }

            if (! $this->modules->validate($dependency)) {
                return false;
            }
        }

        if ($parent = $theme->getParent()) {
            return $this->validate($parent);
        }

        return true;
    }

    public function boot(): void
    {
        $themeName = settings_get('lcframework.themes.enabled');
        if ($themeName === null) {
            return;
        }

        $theme = $this->find($themeName);
        if ($theme === null) {
            return;
        }

        $this->enabledTheme = $theme;

        if (! $this->validate($theme)) {
            $this->disable();

            return;
        }

        foreach ($theme->getDependencies() as $dependency => $version) {
            $module = $this->modules->find($dependency);
            if (! $module->enabled()) {
                $this->modules->enable($module);
            }
        }

        $this->bootProviders($theme);
    }

    public function delete(string|Theme $theme): bool
    {
        if (! ($theme instanceof Theme)) {
            $theme = $this->find($theme);
        }

        if ($theme === null) {
            return false;
        }

        if (! File::deleteDirectory($theme->getPath())) {
            return false;
        }

        if (app()->isProduction()) {
            exec('composer dump-autoload');
        } else {
            exec('composer dump-autoload --optimize');
        }

        if (
            ($enabledTheme = $this->enabled()) &&
            $enabledTheme->getName() === $theme->getName()
        ) {
            $this->disable();
        } else {
            $this->clearCache();
        }

        return true;
    }

    public function install(string $path): bool
    {
        if (! $this->installer->install($path)) {
            return false;
        }

        $this->load();

        return true;
    }

    protected function bootProviders(Theme $theme): void
    {
        (new ProviderRepository(
            $this->app,
            $this->app['files'],
            $this->getManifestPath($theme)
        ))->load($theme->getProviders());
    }

    protected function getManifestPath(Theme $theme): string
    {
        $name = Str::snake(str_replace('/', '_', $theme->getName()));

        if (env('VAPOR_MAINTENANCE_MODE') === null) {
            return Str::replaceLast(
                'config.php',
                $name.'_theme.php',
                $this->app->getCachedConfigPath()
            );
        }

        return Str::replaceLast(
            'services.php',
            $name.'_theme.php',
            $this->app->getCachedServicesPath()
        );
    }

    protected function loadCache(): bool
    {
        if (! $this->isCacheEnabled()) {
            return false;
        }

        $cacheKey = $this->getCacheKey();

        $all = Cache::get($cacheKey);
        if ($all === null) {
            return false;
        }

        $this->themes = [];

        foreach ($all as $name => $theme) {
            $this->themes[$name] = $this->loader->fromArray($theme);
        }

        return true;
    }

    protected function load(): void
    {
        $this->themes = [];

        $paths = $this->getPaths();
        foreach ($paths as $path) {
            $discoveredPaths = $this->discover($path);
            $this->registerPaths($discoveredPaths);
        }

        if ($this->isCacheEnabled()) {
            $cacheKey = $this->getCacheKey();

            Cache::forever(
                $cacheKey,
                collect($this->all())
                    ->mapWithKeys(fn (Theme $theme, string $name): array => [
                        $name => $theme->toArray(),
                    ])
                    ->all()
            );
        }
    }

    protected function clearCache(): void
    {
        if (! $this->isCacheEnabled()) {
            return;
        }

        $cacheKey = $this->getCacheKey();

        Cache::forget($cacheKey);
    }

    protected function discover(string $path): array
    {
        $search = rtrim($path, '/\\').'/'.'composer.json';

        return str_replace('composer.json', '', File::find($search));
    }

    protected function registerPaths(array $paths): void
    {
        foreach ($paths as $path) {
            $this->registerPath($path);
        }
    }

    protected function registerPath(string $path): void
    {
        $theme = $this->loader->fromPath($path);

        $this->themes[$theme->getName()] = $theme;
    }

    protected function getCacheKey(): string
    {
        return config('lcframework.themes.cache.key');
    }

    protected function isCacheEnabled(): bool
    {
        return (bool) config('lcframework.themes.cache.enabled', true);
    }

    protected function getPaths(): array
    {
        return (array) config('lcframework.themes.paths');
    }
}
