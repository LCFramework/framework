<?php

namespace LCFramework\Framework\Module\Repository;

use Filament\Notifications\Notification;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\ProviderRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LCFramework\Framework\Module\Exception\ModuleNotFoundException;
use LCFramework\Framework\Module\Installer\ModuleInstallerInterface;
use LCFramework\Framework\Module\Loader\ModuleLoaderInterface;
use LCFramework\Framework\Module\Module;
use MJS\TopSort\CircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use MJS\TopSort\Implementations\FixedArraySort;

class ModuleRepository implements ModuleRepositoryInterface
{
    protected Application $app;

    protected ModuleLoaderInterface $loader;

    private ModuleInstallerInterface $installer;

    protected ?array $modules = null;

    protected ?array $ordered = null;

    public function __construct(
        Application              $app,
        ModuleLoaderInterface    $loader,
        ModuleInstallerInterface $installer
    )
    {
        $this->app = $app;
        $this->loader = $loader;
        $this->installer = $installer;
    }

    public function all(): array
    {
        if ($this->modules !== null) {
            return $this->modules;
        }

        if (!$this->loadCache()) {
            $this->load();
        }

        return $this->modules;
    }

    public function ordered(): array
    {
        if ($this->ordered !== null) {
            return $this->ordered;
        }

        if (!$this->loadCache()) {
            $this->load();
        }

        return $this->ordered;
    }

    public function enabled(): array
    {
        return collect($this->all())
            ->filter(fn(Module $module): bool => $module->enabled())
            ->all();
    }

    public function disabled(): array
    {
        return collect($this->all())
            ->filter(fn(Module $module): bool => $module->disabled())
            ->all();
    }

    public function status(string $status): array
    {
        return collect($this->all())
            ->filter(fn(Module $module): bool => $module->getStatus() === $status)
            ->all();
    }

    public function enable(string|Module $module, ?string &$reason = null): bool
    {
        if (!($module instanceof Module)) {
            $module = $this->find($module);
        }

        if (!$this->validate($module, $reason)) {
            return false;
        }

        // We have to enable all the dependencies of this module
        // that's being enabled
        foreach ($module->getDependencies() as $dependency) {
            $this->enable($dependency);
        }

        $this->setStatus($module, 'enabled');

        $this->bootProviders($module);

        return true;
    }

    public function disable(string|Module $module): void
    {
        if (!($module instanceof Module)) {
            $module = $this->findOrFail($module);
        }

        $name = $module->getName();

        // We have to disable all modules that are dependent on this module
        // that's being disabled
        foreach ($this->ordered() as $dependent) {
            if (in_array($name, $dependent->getDependencies())) {
                $this->disable($dependent);
            }
        }

        $this->setStatus($module, 'disabled');

        $this->ordered = null;
        $this->load();
    }

    public function setStatus(string|Module $module, string $status): void
    {
        if (!($module instanceof Module)) {
            $module = $this->findOrFail($module);
        }

        $module->setStatus($status);

        settings_put(
            'lcframework.modules.' . $module->getName(),
            $status
        );

        $this->clearCache();
    }

    public function find(string $name): ?Module
    {
        return $this->modules[$name] ?? null;
    }

    public function findOrFail(string $name): Module
    {
        $module = $this->find($name);
        if ($module === null) {
            throw ModuleNotFoundException::module($name);
        }

        return $module;
    }

    public function validate(string|null|Module $module, ?string &$reason = null): bool
    {
        if ($module !== null && !($module instanceof Module)) {
            $module = $this->find($module);
        }

        if ($module === null) {
            $reason = 'Module cannot be found';
            return false;
        }

        if (!File::exists($module->getPath('vendor/autoload.php'))) {
            $reason = 'Module does not have an autoloader configured.';
            return false;
        }

        foreach ($module->getDependencies() as $dependency => $version) {
            if (!($dependencyModule = $this->find($dependency))) {
                $reason = sprintf('Cannot find dependency "%s"', $dependency);
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

            if (!$this->validate($dependency, $reason)) {
                return false;
            }
        }

        $reason = null;

        return true;
    }

    public function boot(): void
    {
        $invalidModules = [];
        foreach ($this->ordered() as $module) {
            if (!$this->validate($module, $reason)) {
                $invalidModules[$module->getName()] = $reason;
            }
        }

        $isAdmin = (
            ($user = auth()->user()) &&
            $user->hasPermissionTo('view admin')
        );

        foreach ($invalidModules as $name => $reason) {
            $this->disable($name);

            if($isAdmin) {
                Notification::make()
                    ->danger()
                    ->title('Theme disabled')
                    ->body($reason)
                    ->send();
            }
        }

        foreach ($this->ordered() as $module) {
            require_once $module->getPath('vendor/autoload.php');

            $this->bootProviders($module);
        }
    }

    public function install(string $path, ?string &$reason = null): bool
    {
        if (!($name = $this->installer->install($path, $reason))) {
            return false;
        }

        $this->modules = null;
        $this->ordered = null;

        $this->load();

        $module = $this->find($name);

        if (!$this->validate($module, $reason)) {
            $this->delete($module);

            return false;
        }

        require_once $module->getPath('vendor/autoload.php');
        $this->installer->publish($module->getProviders());

        return true;
    }

    protected function bootProviders(Module $module): void
    {
        (new ProviderRepository(
            $this->app,
            $this->app['files'],
            $this->getManifestPath($module)
        ))->load(
            collect($module->getProviders())
                ->filter(fn(string $provider): bool => class_exists($provider))
                ->all()
        );
    }

    protected function getManifestPath(Module $module): string
    {
        $name = Str::snake(str_replace('/', '_', $module->getName()));

        if (env('VAPOR_MAINTENANCE_MODE') === null) {
            return Str::replaceLast(
                'config.php',
                $name . '_module.php',
                $this->app->getCachedConfigPath()
            );
        }

        return Str::replaceLast(
            'services.php',
            $name . '_module.php',
            $this->app->getCachedServicesPath()
        );
    }

    protected function loadCache(): bool
    {
        if (!$this->isCacheEnabled()) {
            return false;
        }

        ['all' => $allCacheKey, 'ordered' => $orderedCacheKey] = $this->getCacheKeys();

        $all = Cache::get($allCacheKey);
        $ordered = Cache::get($orderedCacheKey);
        if ($all === null || $ordered === null) {
            return false;
        }

        $this->modules = [];
        $this->ordered = [];

        foreach ($all as $name => $module) {
            $this->modules[$name] = $this->loader->fromArray($module);
        }

        foreach ($ordered as $name) {
            $this->ordered[] = $this->findOrFail($name);
        }

        return true;
    }

    public function load(): void
    {
        if ($this->modules === null) {
            $this->modules = [];

            $paths = $this->getPaths();
            foreach ($paths as $path) {
                $discoveredPaths = $this->discover($path);
                $this->registerPaths($discoveredPaths);
            }

            $statuses = settings('lcframework.modules', []);
            foreach ($statuses as $name => $status) {
                $this->find($name)?->setStatus($status);
            }

            if ($this->isCacheEnabled()) {
                ['all' => $allCacheKey] = $this->getCacheKeys();

                Cache::forever(
                    $allCacheKey,
                    collect($this->all())
                        ->mapWithKeys(fn(Module $module, string $name): array => [
                            $name => $module->toArray(),
                        ])
                        ->all()
                );
            }
        }

        $this->loadOrdered();
    }

    public function delete(string|Module $module, ?string &$reason = null): bool
    {
        if (!($module instanceof Module)) {
            $module = $this->find($module);
        }

        if ($module === null || !File::exists($module->getPath())) {
            settings_forget('lcframework.modules.' . $module->getName());
            $this->clearCache();

            $reason = null;

            return true;
        }

        if (!File::deleteDirectory($module->getPath())) {
            $reason = 'Invalid permissions';
            return false;
        }

        $this->setStatus($module, 'deleted');

        settings_forget('lcframework.modules.' . $module->getName());

        $reason = null;

        return true;
    }

    protected function clearCache(): void
    {
        if (!$this->isCacheEnabled()) {
            return;
        }

        ['all' => $allCacheKey, 'ordered' => $orderedCacheKey] = $this->getCacheKeys();

        Cache::forget($allCacheKey);
        Cache::forget($orderedCacheKey);
    }

    protected function loadOrdered(): void
    {
        if ($this->ordered !== null) {
            return;
        }

        $this->ordered = [];

        $names = $this->getOrderedNames();
        foreach ($names as $name) {
            $this->ordered[] = $this->findOrFail($name);
        }

        if ($this->isCacheEnabled()) {
            ['ordered' => $orderedCacheKey] = $this->getCacheKeys();

            Cache::forever(
                $orderedCacheKey,
                collect($this->ordered())
                    ->map(fn(Module $module): string => $module->getName())
                    ->all()
            );
        }
    }

    protected function getOrderedNames(): array
    {
        $sorter = new FixedArraySort();

        $modules = $this->enabled();
        foreach ($modules as $name => $module) {
            $sorter->add($name, $module->getDependencies());
        }

        $names = [];

        $maxAttempts = count($modules);

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            try {
                $names = $sorter->sort();
                break;
            } catch (CircularDependencyException $e) {
                foreach ($e->getNodes() as $name) {
                    $this->disable($name);
                }
            } catch (ElementNotFoundException $e) {
                $this->disable($e->getSource());
            }
        }

        return $names;
    }

    protected function discover(string $path): array
    {
        $search = rtrim($path, '/\\') . '/' . 'composer.json';

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
        $module = $this->loader->fromPath($path);

        $this->modules[$module->getName()] = $module;
    }

    protected function getCacheKeys(): array
    {
        return (array)config('lcframework.modules.cache.keys');
    }

    protected function isCacheEnabled(): bool
    {
        return (bool)config('lcframework.modules.cache.enabled', true);
    }

    protected function getPaths(): array
    {
        return (array)config('lcframework.modules.paths');
    }
}
