<?php

namespace LCFramework\Framework\Setting\Drivers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

class SettingsFileDriver extends SettingsDriverBase
{
    protected Filesystem $files;

    protected bool $booted = false;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function get(string $key, $default = null)
    {
        return Arr::get($this->data, $key, function () use ($key, $default) {
            $this->data = $this->boot($key, $default);

            return Arr::get($this->data, $key, $default);
        });
    }

    public function save(): void
    {
        if (empty($this->updated) && empty($this->deleted)) {
            return;
        }

        $path = $this->getPath();

        $this->files->put($path, $this->encode($this->data));

        $this->setCache('file', $this->data);
    }

    protected function boot(string $key, $default)
    {
        // If boot has been called twice then the setting doesn't exist
        // so let's just return the default
        if ($this->booted) {
            return $default;
        }

        $this->booted = true;

        if (! ($value = $this->loadCache($key))) {
            $value = $this->load($key) ?? [];
        }

        return $value;
    }

    protected function load(string $key)
    {
        $path = $this->getPath();

        if (! $this->files->exists($path)) {
            return [];
        }

        return $this->decode($this->files->get($path));
    }

    protected function getPath(): string
    {
        return config('lcframework.settings.file.path');
    }

    protected function getCacheKey(string $key): string
    {
        return config('lcframework.settings.cache.key');
    }
}
