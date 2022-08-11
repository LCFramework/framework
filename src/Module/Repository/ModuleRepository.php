<?php

namespace LCFramework\Framework\Module\Repository;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use LCFramework\Framework\Module\Exception\ModuleNotFoundException;
use LCFramework\Framework\Module\Loader\ModuleLoaderInterface;
use LCFramework\Framework\Module\Module;
use MJS\TopSort\CircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use MJS\TopSort\Implementations\FixedArraySort;

class ModuleRepository implements ModuleRepositoryInterface
{
    protected ModuleLoaderInterface $loader;

    protected ?array $modules = null;

    protected ?array $ordered = null;

    public function __construct(ModuleLoaderInterface $loader)
    {
        $this->loader = $loader;
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

    public function enable(string|Module $module): void
    {
        $this->setStatus($module, 'enabled');
    }

    public function disable(string|Module $module): void
    {
        $this->setStatus($module, 'disabled');
    }

    public function setStatus(string|Module $module, string $status): void
    {
        if (!($module instanceof Module)) {
            $module = $this->findOrFail($module);
        }

        $module->setStatus($status);
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
        $paths = $this->getPaths();

        $this->modules = [];

        foreach ($paths as $path) {
            $discoveredPaths = $this->discover($path);
            $this->registerPaths($discoveredPaths);
        }

        if ($this->isCacheEnabled()) {
            ['all' => $allCacheKey] = $this->getCacheKeys();

            Cache::forever(
                $allCacheKey,
                collect($this->all())
                    ->mapWithKeys(fn(Module $module, string $name): array => [
                        $name => $module->toArray()
                    ])
                    ->all()
            );
        }

        $this->loadOrdered();
    }

    protected function loadOrdered(): void
    {
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
        $search = rtrim($path, '/\\') . DIRECTORY_SEPARATOR . 'composer.json';

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
        return config('lcframework.modules.cache.keys');
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
