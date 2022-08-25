<?php

namespace LCFramework\Framework\Theme\Repository;

use Filament\Notifications\Notification;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\ProviderRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LCFramework\Framework\Module\Repository\ModuleRepositoryInterface;
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

    public function enable(Theme|string $theme, ?string &$reason = null): bool
    {
        if (! ($theme instanceof Theme)) {
            $theme = $this->find($theme);
        }

        if (! $this->validate($theme, $reason)) {
            return false;
        }

        settings_put('lcframework.themes.enabled', $theme->getName());

        $this->clearCache();

        $this->boot();

        return true;
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

    public function validate(string|null|Theme $theme, ?string &$reason = null): bool
    {
        if ($theme !== null && ! ($theme instanceof Theme)) {
            $theme = $this->find($theme);
        }

        if ($theme === null) {
            $reason = 'Theme cannot be found';

            return false;
        }

        if (! File::exists($theme->getPath('vendor/autoload.php'))) {
            $reason = 'Theme does not have an autoloader configured.';

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
                $reason = sprintf(
                    'Dependency "%s" does not satisfy the %s requirement (installed: %s)',
                    $dependency,
                    $version,
                    $dependencyVersion
                );

                return false;
            }

            if (! $this->modules->validate($dependency, $reason)) {
                return false;
            }
        }

        if ($parent = $theme->getParent()) {
            return $this->validate($parent, $reason);
        }

        $reason = null;

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

        if (! $this->validate($theme, $reason)) {
            $this->disable();

            if (
                ($user = auth()->user()) &&
                $user->hasPermissionTo('view admin')
            ) {
                Notification::make()
                    ->danger()
                    ->title('Theme disabled')
                    ->body($reason)
                    ->send();
            }

            return;
        }

        require_once $theme->getPath('vendor/autoload.php');

        foreach ($theme->getDependencies() as $dependency => $version) {
            $module = $this->modules->find($dependency);
            if (! $module->enabled()) {
                $this->modules->enable($module);
            }
        }

        $this->bootProviders($theme);
    }

    public function delete(string|Theme $theme, ?string &$reason = null): bool
    {
        if (! ($theme instanceof Theme)) {
            $theme = $this->find($theme);
        }

        if ($theme === null || ! File::exists($theme->getPath())) {
            $this->clearCache();

            $reason = null;

            return true;
        }

        if (! File::deleteDirectory($theme->getPath())) {
            $reason = 'Invalid permissions';

            return false;
        }

        if (
            ($enabledTheme = $this->enabled()) &&
            $enabledTheme->getName() === $theme->getName()
        ) {
            $this->disable();
        } else {
            $this->clearCache();
        }

        $reason = null;

        return true;
    }

    public function install(string $path, ?string &$reason = null): bool
    {
        if (! ($name = $this->installer->install($path, $reason))) {
            return false;
        }

        $this->load();

        $theme = $this->find($name);

        if (! $this->validate($theme, $reason)) {
            $this->delete($theme);

            return false;
        }

        require_once $theme->getPath('vendor/autoload.php');
        $this->installer->publish($theme->getProviders());

        return true;
    }

    protected function bootProviders(Theme $theme): void
    {
        (new ProviderRepository(
            $this->app,
            $this->app['files'],
            $this->getManifestPath($theme)
        ))->load(
            collect($theme->getProviders())
                ->filter(fn (string $provider): bool => class_exists($provider))
                ->all()
        );
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
